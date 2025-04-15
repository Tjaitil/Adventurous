<?php

namespace App\Services;

use App\Models\ConversationTracker;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ReflectionMethod;

/**
 * Explanation of the structure
 * Each conversation starts with an index
 *
 * Then each index will follow a similar pattern
 * index {
 *  id: int,
 *  header: string,
 *  client_events: string[] Fired when conversation is fetched on frontend,
 *  options: [
 *    {
 *      "person": person|player,
 *      "container": A | B,
 *      "text": string,
 *      "replacers": ["EventName@methodName"],
 *      "conditional": string,
 *      "next_key": "S",
 *      "id": 0
 *    }
 *  ]
 *  server_events: [
 *   {
 *    "event": "EventName@methodName",
 *   "type": "has",
 *  "results": ["S", "E"],
 *  "callbacks": server_events[]
 *  }
 * ]
 * }
 *
 * Q - main questions
 * q - question
 * r - response
 * end - end of conversation
 * S - server event
 */
class ConversationService
{
    public int $selectedId;

    public string $newIndex;

    /**
     * @var array<string, mixed>
     */
    public array $oldConversation;

    /**
     * @var array<string, mixed>
     */
    public array $conversationFile;

    protected ConversationTracker $ConversationTracker;

    public function __construct(private ConversationParamService $conversationParamService) {}

    public function getConversationTracker(): void
    {
        $this->ConversationTracker = ConversationTracker::where('user_id', Auth::user()?->id)->firstOrFail()->refresh();
    }

    /**
     * @return array<string,mixed>|void
     */
    public function getConversation(string $person, int $id, bool $start)
    {
        $this->getConversationTracker();
        $this->selectedId = $id;
        $this->conversationFile = $this->loadPerson($person);

        if ($start === true) {
            $index = $this->conversationFile['index'];
            $this->setNewIndex($index);
            $this->ConversationTracker->selected_option_values = null;
            $this->ConversationTracker->save();
        } else {

            $newIndex = $this->ConversationTracker->current_index ?? '';
            $this->setNewIndex($newIndex);
            $this->oldConversation = $this->getNextLine($this->newIndex, $this->conversationFile);
            $matchedOption = current(array_filter($this->oldConversation['options'] ?? [], fn ($key) => $key['id'] === $id));
            if ($matchedOption === false) {
                throw new Exception(sprintf('Option %s not found', $id), 422);
            }
            $newIndex = $this->newIndex .= $matchedOption['next_key'];

            if (isset($matchedOption['option_values'])) {

                ConversationTracker::where('user_id', Auth::user()?->id)
                    ->update(['selected_option_values' => $matchedOption['option_values']]);
            }
            $this->setNewIndex($newIndex);
        }

        $currentConversation = $this->getNextLine($this->newIndex, $this->conversationFile);

        $currentConversation = $this->checkForServerEvents($currentConversation);

        $currentConversation = $this->checkForConditional($currentConversation);

        $currentConversation = $this->checkForTextPlaceholders($currentConversation,
            $this->ConversationTracker->selected_option_values);

        unset($currentConversation['serverEvents']);

        return $currentConversation;
    }

    private function setNewIndex(string $newIndex): void
    {
        $this->newIndex = $newIndex;
        $this->ConversationTracker->current_index = $this->newIndex;
        $this->ConversationTracker->save();
        $this->ConversationTracker->refresh();
    }

    /**
     * @param  array<string, mixed>  $currentConversation
     * @return array<string, mixed>
     */
    public function checkForServerEvents(array $currentConversation): array
    {
        if (isset($currentConversation['server_events'])) {
            $result = $this->triggerServerEvents($currentConversation['server_events']);
            if ($result !== null) {
                $newIndex = $this->newIndex.$result;
                $this->setNewIndex($newIndex);
                $currentConversation = $this->getNextLine($newIndex, $this->conversationFile);
            }
        }

        return $currentConversation;
    }

    /**
     * @param  array<int, mixed>  $serverEvents
     */
    private function triggerServerEvents(array $serverEvents): ?string
    {
        $nextIndex = null;
        foreach ($serverEvents as $key => $serverEvent) {
            if ($serverEvent['option_values'] === '__CURRENT_SELECTED_OPTION_VALUES__') {
                $serverEvent['option_values'] = $this->ConversationTracker->selected_option_values;
            }
            $result = $this->conversationParamService->invokeServerEvent($serverEvent['event'], $serverEvent['option_values']);
            if ($result === true) {
                $nextIndex = $serverEvent['results'][0];
            } else {
                $nextIndex = $serverEvent['results'][1];
            }
        }

        return $nextIndex;
    }

    /**
     * Get className and methodName from $eventString from the $event variable.
     *
     * @return array{0: string, 1: string}
     */
    protected function getClassNameAndMethodName(string $event): array
    {
        $eventParts = explode('@', $event);
        $className = 'App\\Conversation\\ServerEvents\\'.$eventParts[0];
        $methodName = $eventParts[1];
        $result = [$className, $methodName];

        return $result;
    }

    /**
     * @param  array<string, mixed>  $conversation
     * @return array<string, mixed>
     */
    public function getNextLine(?string $index, array $conversation): array
    {
        $matchedValue = null;
        foreach ($conversation as $key => $value) {
            if ($key === $index) {
                $matchedValue = $value;
                break;
            }
        }

        if ($matchedValue === null) {
            throw new Exception(sprintf('Conversation line %s not found', $index), 422);
        }

        return $matchedValue;
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \InvalidArgumentException
     * @throws \League\Flysystem\FilesystemException
     * @throws \Exception
     */
    public function loadPerson(string $person)
    {
        $person = strtolower($person);

        $file = Storage::disk('gamedata')->json('conversations/'.$person.'.json');
        if (is_null($file)) {
            $file = require storage_path('app/gamedata/conversations/'.$person.'.php');
        }

        if (is_null($file)) {
            throw new Exception('Conversation file not found', 422);
        }

        return $file;
    }

    public function hasMethodParameter(string|object $class, string $methodName): bool
    {
        $reflection = new ReflectionMethod($class, $methodName);

        return $reflection->getNumberOfParameters() > 0;
    }

    public function resolveMethodArgs(string $class, string $methodName, ?array $optionValues = []): array
    {
        $reflection = new ReflectionMethod($class, $methodName);
        $parameters = $reflection->getParameters();
        $args = [];
        foreach ($parameters as $parameter) {
            if ($parameter->getType()->getName() === \App\Models\User::class) {
                $args[] = Auth::user();

                continue;
            }

            $name = $parameter->getName();
            if (! isset($optionValues[$name])) {
                throw new Exception(sprintf('Option value %s not found', $name));
            }

        }

        return $args;
    }

    /**
     * @param  array<string, mixed>  $currentConversation
     * @return array<string, mixed>
     */
    public function checkForConditional(array $currentConversation): array
    {
        $idToRemove = [];
        // dd($currentConversation['options']);
        foreach ($currentConversation['options'] as $key => $value) {
            if (isset($value['conditional'])) {
                $result = $this->handleConditional($value['conditional'], $value['option_values']);
                if ($result === false) {
                    $idToRemove[] = $value['id'];
                }
            }
        }

        if (count($idToRemove) > 0) {
            $currentConversation['options'] =
                array_values(array_filter($currentConversation['options'], fn ($key) => ! in_array($key['id'], $idToRemove)));
        }

        return $currentConversation;
    }

    /**
     * Check if the class is an event handler extending \App\Conversation\Handlers\BaseHandler
     */
    public function isEventHandlerClass(string $className): bool
    {
        return str_contains($className, 'Handler');
    }

    public function handleConditional(string $conditional, array $option_values): bool
    {
        return $this->conversationParamService->invokeConditional($conditional, $option_values);
    }

    /**
     * @param  array<string, mixed>  $currentConversation
     * @return array<string, mixed>
     */
    public function checkForTextPlaceholders(array $currentConversation, ?array $option_values): array
    {

        foreach ($currentConversation['options'] as $key => $option) {

            if (! isset($option['replacers'])) {
                continue;
            }
            foreach ($option['replacers'] as $replaceKey => $value) {
                $option['replacers'][$replaceKey] = $this->conversationParamService->invokeReplacer($value, $option_values);

                $currentConversation['options'][$key]['text'] = __($option['text'], $option['replacers']);
            }
        }

        return $currentConversation;
    }
}
