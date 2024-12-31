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

    public function __construct() {}

    public function getConversationTracker(): void
    {
        $this->ConversationTracker = ConversationTracker::where('user_id', Auth::user()?->id)->firstOrFail();
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
            $this->ConversationTracker->conversation_option_value = null;
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
            if (isset($matchedOption['option_value'])) {
                ConversationTracker::where('user_id', Auth::user()?->id)
                    ->update(['conversation_option_value' => $matchedOption['option_value']]);
            }
            $this->setNewIndex($newIndex);
        }

        $currentConversation = $this->getNextLine($this->newIndex, $this->conversationFile);

        $currentConversation = $this->checkForServerEvents($currentConversation);

        $currentConversation = $this->checkForConditional($currentConversation);

        $currentConversation = $this->checkForTextPlaceholders($currentConversation,
            $this->ConversationTracker->conversation_option_value);

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
            [$class, $className, $methodName] = $this->getClassInstance($serverEvent['event']);
            if (method_exists($class, $methodName)) {
                $hasParameter = $this->hasMethodParameter($class, $methodName);
                if ($serverEvent['type'] === 'has') {
                    if ($hasParameter) {
                        $selected_option_value = $this->ConversationTracker->conversation_option_value;

                        $result = $class->{$methodName}($selected_option_value);
                        if ($result === true) {
                            $nextIndex = $serverEvent['results'][0];
                        } else {
                            $nextIndex = $serverEvent['results'][1];
                        }
                    } else {
                        $nextIndex = $class->{$methodName}();
                    }

                } elseif ($serverEvent['type'] === 'handle') {
                    $selected_option_value = $this->ConversationTracker->conversation_option_value;
                    $class->{$methodName}($selected_option_value);
                }
                if (isset($serverEvent['callbacks'])) {
                    $this->triggerServerEvents($serverEvent['callbacks']);
                }
            } else {
                throw new Exception(sprintf('Method %s not found in class %s', $methodName, $className), 422);
            }
        }

        return $nextIndex;
    }

    /**
     * @return array{0: object, 1: class-string, 2: string}
     */
    private function getClassInstance(string $event)
    {
        [$className, $methodName] = $this->getClassNameAndMethodName($event);
        if (! class_exists($className)) {
            throw new Exception(sprintf('Class %s not found', $className), 422);
        }

        return [app()->make($className), $className, $methodName];
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
        if (! is_array($file)) {
            throw new Exception('Conversation file not found', 422);
        }

        return $file;
    }

    public function hasMethodParameter(string|object $class, string $methodName): bool
    {
        $reflection = new ReflectionMethod($class, $methodName);

        return $reflection->getNumberOfParameters() > 0;
    }

    /**
     * @param  array<string, mixed>  $currentConversation
     * @return array<string, mixed>
     */
    public function checkForConditional(array $currentConversation): array
    {
        foreach ($currentConversation['options'] as $key => $value) {
            if (isset($value['conditional'])) {
                $conditional = $value['conditional'];
                $result = $this->handleConditional($conditional, $value['option_value']);
                if ($result === false) {
                    unset($currentConversation['options'][$key]);
                    // Reindex the array
                    $currentConversation['options'] = array_values($currentConversation['options']);
                }
            }
        }

        return $currentConversation;
    }

    public function handleConditional(string $conditional, string $option_value): bool
    {
        [$class, $className, $methodName] = $this->getClassInstance($conditional);

        if (method_exists($class, $methodName)) {
            $hasParameter = $this->hasMethodParameter($class, $methodName);
            $result = $hasParameter ? $class->{$methodName}($option_value) : $class->{$methodName}();
        } else {
            throw new Exception(sprintf('Method %s not found in class %s', $methodName, $className), 422);
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $currentConversation
     * @return array<string, mixed>
     */
    public function checkForTextPlaceholders(array $currentConversation, ?string $option_value): array
    {

        foreach ($currentConversation['options'] as $key => $option) {

            if (! isset($option['replacers'])) {
                continue;
            }
            foreach ($option['replacers'] as $replaceKey => $value) {
                [$class, $className, $methodName] = $this->getClassInstance($value);
                if (method_exists($class, $methodName)) {
                    $option['replacers'][$replaceKey] = $class->{$methodName}($option_value);
                }
                $currentConversation['options'][$key]['text'] = __($option['text'], $option['replacers']);
            }
        }

        return $currentConversation;
    }
}
