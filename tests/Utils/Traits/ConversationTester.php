<?php

namespace Tests\Utils\Traits;

use App\Models\ConversationTracker;
use App\Services\ConversationService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait ConversationTester
{
    public array $hasServerEventTestCallbacks;

    public ConversationTracker $ConversationTracker;

    public ConversationService $ConversationService;

    public array $conversationFile;

    public function __constructConversationTester(string $person)
    {
        $this->ConversationService = app()->make(ConversationService::class);
        $this->ConversationTracker = ConversationTracker::where('user_id', $this->RandomUser->id)->firstOrFail();
        $this->actingAs($this->getRandomUser());
        $this->conversationFile = Storage::disk('gamedata')->json("conversations/{$person}.json");
    }

    public function triggerConversationTree(string $person): void
    {
        try {
            $conversation = $this->ConversationService->getConversation($person, 0, true);
            foreach ($conversation['options'] as $key => $option) {
                $this->checkConversationTree($person, $conversation);
            }
        } catch (Exception $e) {
            Log::info($e->getTraceAsString(), ['index' => $this->ConversationTracker->current_index]);
            $this->fail($this->ConversationTracker->refresh()->current_index.' '.$e->getMessage().$e->getFile().$e->getLine());
        }
    }

    public function checkConversationTree(string $person, array $object)
    {
        if ($object['options'] === null && $object['server_event'] === null) {
            $this->fail('No options found for conversation segment');
        } elseif (is_array($object['options'])) {
            $parentKey = $object['index'];
            foreach ($object['options'] as $key => $value) {
                $this->ConversationTracker->current_index = $parentKey;
                $this->ConversationTracker->save();

                $this->assertArrayHasKey('id', $value);
                $this->assertArrayHasKey('next_key', $value);
                if ($value['next_key'] === 'end') {
                    continue;

                } elseif ($value['next_key'] === 'S') {
                    $this->checkServerEvents($value, $parentKey);
                }
                $conversation = $this->ConversationService->getConversation($person, $value['id'], false);
                $this->checkConversationTree($person, $conversation);
            }
        }
    }

    public function checkServerEvents(array $optionValue, string $parentKey)
    {
        $index = $parentKey.'S';

        $conversation = $this->conversationFile[$index];
        if ($conversation === null) {
            $this->fail('Conversation segment not found when trying test server_events');
        }
        // Check if server events array contains a 'has' server event
        $hasServerEvent = array_filter($conversation['server_events'], fn ($key) => $key['type'] === 'has');
        if ($hasServerEvent) {

            // Call callbacks to test both states of server events
            $this->getServerEventCallbacks($index, 0)();
            $conversation = $this->ConversationService->getConversation('kapys', $optionValue['id'], false);

            // Reset the conversation index to the parent key. Somehow the conversationtracker save() doesnt work
            ConversationTracker::where('user_id', $this->RandomUser->id)->update(['current_index' => $parentKey]);

            $this->getServerEventCallbacks($index, 1)();
            $conversation = $this->ConversationService->getConversation('kapys', $optionValue['id'], false);

            // Reset the conversation index to the parent key. Somehow the conversationtracker save() doesnt work
            ConversationTracker::where('user_id', $this->RandomUser->id)->update(['current_index' => $parentKey]);
        }
    }

    public function getServerEventCallbacks(string $id, $index)
    {
        return $this->hasServerEventTestCallbacks[$id][$index];
    }
}
