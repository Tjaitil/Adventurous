<?php

namespace App\Services;

use App\Conversation\Handlers\BaseHandler;
use App\Dto\Conversation\SegmentDto;
use App\Models\ConversationTracker;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

final class ConversationService
{
    public int $selectedId;

    public string $newIndex;

    public BaseHandler $handlerClass;

    public SegmentDto $oldConversation;

    /**
     * @var array<string, mixed>
     */
    public array $conversationFile;

    protected ConversationTracker $ConversationTracker;

    public function __construct(private ConversationCallableService $conversationParamService) {}

    private function getConversationTracker(): void
    {
        $this->ConversationTracker = ConversationTracker::where('user_id', Auth::user()?->id)->firstOrFail()->refresh();
    }

    /**
     * @throws Exception
     */
    private function loadHandlerClass(string $person): void
    {
        $handlerClass = 'App\\Conversation\\Handlers\\'.ucfirst($person).'Handler';
        if (class_exists($handlerClass)) {
            $this->handlerClass = app()->make($handlerClass);
        } else {
            throw new Exception(sprintf('Handler class not found for person %s', $person), 422);
        }
    }

    /**
     * @return array<string,mixed>
     */
    public function getConversation(string $person, int $selectedOptionId, bool $start): array
    {
        $this->loadHandlerClass($person);
        $this->getConversationTracker();
        $this->selectedId = $selectedOptionId;
        $this->conversationFile = $this->loadPerson($person);

        if ($start === true) {
            $index = $this->conversationFile['index'];
            $this->setNewIndex($index);
            $this->ConversationTracker->selected_option_values = null;
            $this->ConversationTracker->save();
        } else {
            $this->newIndex = $this->ConversationTracker->current_index ?? '';
            $this->oldConversation = $this->getNextSegment($this->newIndex, $this->conversationFile);
            $matchedOption = current(array_filter($this->oldConversation->options ?? [], fn ($key) => $key->id === $selectedOptionId));
            if ($matchedOption === false) {
                throw new Exception(sprintf('Option %s not found', $selectedOptionId), 422);
            }
            $newIndex = $this->newIndex .= $matchedOption->nextKey;

            if (isset($matchedOption->optionValues)) {

                ConversationTracker::where('user_id', Auth::user()?->id)
                    ->update(['selected_option_values' => $matchedOption->optionValues]);
            }
            $this->setNewIndex($newIndex);
        }

        $currentSegment = $this->getNextSegment($this->newIndex, $this->conversationFile);

        $currentSegment = $this->checkForServerEvents($currentSegment);

        $currentSegment = $this->checkForConditional($currentSegment);

        $currentSegment = $this->checkForTextPlaceholders($currentSegment);

        $clientEvents = $this->handlerClass->getClientEvent($this->newIndex);

        $currentSegment = $this->attachClientCallback($currentSegment);

        return [...$currentSegment->toArray(),
            'client_events' => $clientEvents,
        ];
    }

    public function attachClientCallback(SegmentDto $segment): SegmentDto
    {
        foreach ($segment->options as $key => $option) {
            if ($option->hasClientCallback) {

                $optionIdIndex = $this->generateOptionIdIndex($this->newIndex, $option->id);
                $option->setClientCallback(
                    $this->handlerClass->getClientCallBack($optionIdIndex)
                );
            }
        }

        return $segment;
    }

    private function setNewIndex(string $newIndex): void
    {
        $this->newIndex = $newIndex;
        $this->ConversationTracker->current_index = $this->newIndex;
        $this->ConversationTracker->save();

        $this->ConversationTracker->refresh();
    }

    private function checkForServerEvents(SegmentDto $currentSegment): SegmentDto
    {
        if (! $currentSegment->hasServerEvent) {
            return $currentSegment;
        }

        $serverEventname = $this->handlerClass->getServerEvent($this->newIndex);

        if (! is_null($serverEventname)) {
            $nextKey = $this->conversationParamService->invokeServerEvent($this->handlerClass, $serverEventname, $this->ConversationTracker->selected_option_values);
            $newIndex = $this->newIndex.$nextKey;
            $this->setNewIndex($newIndex);
            $currentSegment = $this->getNextSegment($newIndex, $this->conversationFile);
        }

        return $currentSegment;
    }

    /**
     * @param  array<string, mixed>  $conversation
     */
    private function getNextSegment(?string $index, array $conversation): SegmentDto
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

        return new SegmentDto($matchedValue);
    }

    /**
     * @return array<string,mixed>
     *
     * @throws \InvalidArgumentException
     * @throws \League\Flysystem\FilesystemException
     * @throws \Exception
     */
    private function loadPerson(string $person)
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

    private function checkForConditional(SegmentDto $currentSegment): SegmentDto
    {
        $idToRemove = [];
        foreach ($currentSegment->options as $key => $option) {
            if ($option->hasConditional) {

                $conditionalIndex = $this->generateOptionIdIndex($this->newIndex, $option->id);
                $conditional = $this->handlerClass->getConditional($conditionalIndex);

                if (is_null($conditional)) {
                    throw new Exception(sprintf('Conditional %s not found', $conditionalIndex), 422);
                }

                $result = $this->conversationParamService->invokeConditional($this->handlerClass, $conditional, $option->optionValues, $this->ConversationTracker->selected_option_values ?? []);
                if ($result === false) {
                    $idToRemove[] = $option->id;
                }
            }
        }

        if (count($idToRemove) > 0) {
            $currentSegment->options =
                array_values(array_filter($currentSegment->options, fn ($key) => ! in_array($key->id, $idToRemove)));
        }

        return $currentSegment;
    }

    private function checkForTextPlaceholders(SegmentDto $currentSegment): SegmentDto
    {
        foreach ($currentSegment->options as $key => $option) {
            if ($option->hasReplacer === false) {
                continue;
            }

            $replacers = $this->handlerClass->getReplacers($this->generateOptionIdIndex($this->newIndex, $option->id));

            if (is_null($replacers)) {
                continue;
            }
            foreach ($replacers as $placeHolder => $replaceMethod) {
                $replacementstring = $this->conversationParamService->invokeReplacer($this->handlerClass, $replaceMethod, $this->ConversationTracker->selected_option_values ?? []);

                $option->text = str_replace($placeHolder, $replacementstring, $option->text);
            }
        }

        return $currentSegment;
    }

    /**
     * Used to generate the index for the option id to get callables
     */
    private function generateOptionIdIndex(string $index, int $id): string
    {
        return $index.'#'.$id;
    }
}
