<?php

namespace Tests\Feature\Feature\Services;

use App\Models\ConversationTracker;
use App\Services\ConversationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConversationServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->beginDatabaseTransaction();
        $this->actingAs($this->RandomUser);
    }

    public function test_option_values_is_reset_on_conversation_start(): void
    {
        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'selected_option_values' => ['test' => 'test'],
        ]);

        $response = $this->post('/conversation/next', [
            'person' => 'kapys',
            'is_starting' => true,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('conversation_trackers', [
            'user_id' => $this->RandomUser->id,
            'selected_option_values' => null,
        ]);
    }

    public function test_get_conversation_throws_exception_if_option_does_not_exist()
    {
        $this->app->bind('App\Conversation\Handlers\Foo', \App\Conversation\Handlers\FooHandler::class);

        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'current_index' => 'foo',
        ]);

        $conversationService = $this->app->make(ConversationService::class);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Option 100 not found');

        $conversationService->getConversation('Foo', 100, false);
    }

    public function test_conversation_filter_outs_options_with_failed_conditionals()
    {
        $this->app->bind('App\Conversation\Handlers\Foo', \App\Conversation\Handlers\FooHandler::class);

        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'current_index' => 'foo',
        ]);

        $conversationService = $this->app->make(ConversationService::class);
        $result = $conversationService->getConversation('Foo', 0, false);

        $ids = array_map(
            function ($option) {
                return $option['id'];
            },
            $result['options']
        );
        $this->assertNotContains(0, $ids);
    }

    public function test_get_conversation_returns_client_callback_on_correct_options()
    {
        $this->app->bind('App\Conversation\Handlers\Foo', \App\Conversation\Handlers\FooHandler::class);

        $currentIndex = 'foor';
        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'current_index' => $currentIndex,
        ]);

        $conversationService = $this->app->make(ConversationService::class);
        $result = $conversationService->getConversation('Foo', 0, false);

        $this->assertEquals('exampleClientCallback', $result['options'][0]['client_callback']);
        $this->assertEquals(null, $result['options'][1]['client_callback']);
    }

    public function test_get_conversation_replaces_placeholders()
    {
        $this->app->bind('App\Conversation\Handlers\Foo', \App\Conversation\Handlers\FooHandler::class);

        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'current_index' => 'foorr',
        ]);

        $conversationService = $this->app->make(ConversationService::class);
        $result = $conversationService->getConversation('Foo', 0, false);

        $this->assertEquals('hi sir', $result['options'][0]['text']);
    }

    public function test_get_conversation_calls_server_event_with_option_value_from_previous_segment()
    {
        $this->app->bind('App\Conversation\Handlers\Foo', \App\Conversation\Handlers\FooHandler::class);

        ConversationTracker::where('user_id', $this->RandomUser->id)->update([
            'current_index' => 'foorrrr',
        ]);

        $conversationService = $this->app->make(ConversationService::class);
        $conversationService->getConversation('Foo', 0, false);

        $ConversationTracker = ConversationTracker::where('user_id', $this->RandomUser->id)->first();
        $this->assertEquals('foorrrrSr0', $ConversationTracker->current_index);
    }
}

namespace App\Conversation\Handlers;

use App\Attributes\SelectedConversationOptionValue;

class FooHandler extends BaseHandler
{
    /**
     * @var array<string, string>
     */
    protected array $conditionals = [
        'foor#1' => 'exampleConditional',
        'foor#0' => 'exampleConditional',
    ];

    protected array $clientCallBacks = [
        'foorr#0' => 'exampleClientCallback',
    ];

    protected array $replacers = [
        'foorrr#0' => [
            ':foo' => 'fooReplacer',
            ':bar' => 'barReplacer',
        ],
    ];

    protected array $serverEvent = [
        'foorrrrS' => 'exampleServerEvent',
    ];

    public function exampleConditional(int $value)
    {
        return $value === 1;
    }

    public function fooReplacer()
    {
        return 'hi';
    }

    public function barReplacer()
    {
        return 'sir';
    }

    public function exampleServerEvent(#[SelectedConversationOptionValue] string $someValue)
    {
        if ($someValue === 'foo') {
            return 'r0';
        } else {
            return 'r1';
        }
    }
}
