<?php

namespace Tests;

use App\Models\ConversationTracker;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

class ConversationTest extends TestCase
{
    public ConversationTracker $ConversationTracker;

    protected ?string $person;

    protected array $conversation_file = [];

    /**
     * Undocumented variable
     *
     * @var array{string: array{callable, callable}}[]
     */
    protected $serverEventTestCallbacks = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->getRandomUser());

        $this->ConversationTracker = ConversationTracker::where('user_id', $this->RandomUser->id)->firstOrFail();
    }

    public function loadConversationFile(): void
    {
        $file = Storage::disk('gamedata')->json('conversations/'.$this->person.'.json');

        if (is_null($file)) {
            $this->fail('Conversation file not found');
        }
        $this->conversation_file = $file;
    }

    public function triggerConversationTree(string $person): void
    {
        $this->person = $person;
        try {
            error_log('ConversationTest: triggerConversationTree');

            $response = $this->callNext(0, true);

            $this->checkConversationTree($person, $response['conversation_segment']);

            // If next_key is server event then run the callback provided by user for that server event

            // foreach ($conversation['options'] as $key => $option) {
            //     $this->checkConversationTree($person, $conversation);
            // }
        } catch (Exception $e) {
            // Log::info($e->getTraceAsString(), ['index' => $this->ConversationTracker->current_index]);
            $this->fail($this->ConversationTracker->refresh()->current_index.' '.$e->getMessage().$e->getFile().$e->getLine());
        }
    }

    // $person = $request->string('person')->toString();
    // $nextKey = $request->integer('selected_option');
    // $isStarting = $request->boolean('is_starting');

    public function checkConversationTree(string $person, array $conversationSegment)
    {
        if ($conversationSegment['options'] === null && $conversationSegment['server_event'] === null) {
            $this->fail('No options found for conversation segment');
        } elseif (is_array($conversationSegment['options'])) {
            $parentKey = $conversationSegment['index'];
            foreach ($conversationSegment['options'] as $key => $value) {
                error_log('Starting '.$parentKey.' - '.$value['id']);

                $this->setConversationIndex($parentKey);

                $this->assertArrayHasKey('id', $value);
                $this->assertArrayHasKey('next_key', $value);
                if ($value['next_key'] === 'end') {
                    continue;

                } elseif ($value['next_key'] === 'S') {
                    $this->checkServerEvents($value, $parentKey);
                }

                Log::info('Checking conversation tree for '.$parentKey);
                $response = $this->callNext($value['id']);
                error_log(print_r($response, true));

                $this->checkConversationTree($person, $response['conversation_segment']);
                error_log('Finished '.$parentKey.' - '.$value['id']);
            }
        }
    }

    public function checkServerEvents(array $optionValue, string $parentKey)
    {
        $index = $parentKey.'S';
        error_log('Checking server events for index 0 - '.$index);
        $this->getServerEventCallbacks($index, 0)();
        $response = $this->callNext($optionValue['id']);

        $this->checkConversationTree($this->person, $response['conversation_segment']);

        $this->setConversationIndex($parentKey);

        error_log('Checking server events for index 1 -'.$index);
        $this->getServerEventCallbacks($index, 1)();
        $response = $this->callNext($optionValue['id']);
        $this->checkConversationTree($this->person, $response['conversation_segment']);

        $this->setConversationIndex($parentKey);
    }

    public function setConversationIndex(string $index, ?array $selected_conversation_option = null)
    {
        ConversationTracker::where('user_id', Auth::user()?->id)->firstOrFail()->update([
            'current_index' => $index,
            'selected_option_values' => $selected_conversation_option,
        ]);
    }

    protected function callNext(int $selectedOption, $starting = false)
    {
        $response = $this->post('conversation/next', [
            'is_starting' => $starting,
            'person' => $this->person,
            'selected_option' => $selectedOption,
        ]);

        return $response->json();
    }

    public function getServerEventCallbacks(string $id, $index)
    {
        return $this->serverEventTestCallbacks[$id][$index] ?? null;
    }

    public function assertConversationSegment(TestResponse $Response)
    {
        $Response->assertJsonStructure([
            'conversation_segment' => [
                'index',
                'options' => [
                    '*' => [
                        'id',
                        'next_key',
                        'text',
                        'person',
                    ],
                ],
            ],
        ]);
    }
}
