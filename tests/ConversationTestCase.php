<?php

namespace Tests;

use App\Conversation\Handlers\BaseHandler;
use App\Dto\Conversation\SegmentDto;
use App\Models\ConversationTracker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;

class ConversationTestCase extends TestCase
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

    public function check_conversation_structure(string $index)
    {
        $segment = new SegmentDto($this->conversation_file[$index]);

        $lastLetter = substr($index, -1);
        if ($lastLetter === 'S') {
            $parentKey = $segment->index;
            foreach ($segment->server_event_results as $key => $value) {
                $this->assertArrayHasKey($parentKey.$value, $this->conversation_file);
                $this->check_conversation_structure($parentKey.$value);
            }
        } else {
            $parentKey = $segment->index;
            foreach ($segment->options as $key => $option) {
                if ($option->nextKey === 'end') {

                    break;
                }
                $this->assertArrayHasKey($parentKey.$option->nextKey, $this->conversation_file);
                $this->check_conversation_structure($parentKey.$option->nextKey);
            }
        }
    }

    public function check_handler_callables(BaseHandler $handler)
    {
        foreach ($this->conversation_file as $key => $value) {
            if ($key === 'index') {
                continue;
            }
            $segment = new SegmentDto($value);
            if ($segment->hasClientEvent) {
                $result = $handler->getClientEvent($segment->index);
                $this->assertIsArray($result);
            } elseif ($segment->hasServerEvent) {
                $result = $handler->getServerEvent($segment->index);
                if (! is_null($result)) {
                    $this->assertTrue(method_exists($handler, $result), 'Server event not found');
                }
            }
            foreach ($segment->options as $key => $option) {
                $optionIndex = $segment->index.'#'.$option->id;
                if ($option->hasReplacer) {
                    $result = $handler->getReplacers($optionIndex, 'replacers');
                    foreach ($result as $key => $replacer) {
                        method_exists($handler, $replacer) ?: $this->fail('Replacer not found');
                    }
                } elseif ($option->hasConditional) {
                    $result = $handler->getConditional($optionIndex);
                    $this->assertIsString($result);
                    $this->assertTrue(method_exists($handler, $result), 'Conditional not found');
                } elseif ($option->hasClientCallback) {
                    $result = $handler->getClientCallBack($optionIndex, 'clientCallBacks');
                    $this->assertIsString($result);
                }
            }
        }
    }

    /**
     * Set the conversation index before calling the next segment.
     *
     * @param  string  $index  The index before in the conversation tree.
     */
    public function setConversationIndex(string $index, ?array $selected_conversation_option = null): void
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

    /**
     * @param  callable  $callback
     * @param  int  $index
     */
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
