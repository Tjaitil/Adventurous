<?php

namespace Tests\Feature\Services;

use App\Attributes\SelectedConversationOptionValue;
use App\Conversation\Handlers\BaseHandler;
use App\Models\User;
use App\Services\ConversationCallableService;
use Illuminate\Container\Attributes\CurrentUser;
use Tests\TestCase;

class ConversationCallableServiceTest extends TestCase
{
    public function test_invoke_server_events_with_correct_parameters(): void
    {
        $User = $this->getRandomUser();
        $this->actingAs($User);

        $service = $this->app->make(ConversationCallableService::class);

        $result = $service->invokeServerEvent(
            new TestHandler,
            'testMethod',
            [
                'location' => 'snerpiir',
            ]
        );

        $this->assertEquals('r1', $result);
    }
}

class TestHandler extends BaseHandler
{
    public function testMethod(#[SelectedConversationOptionValue] string $location, #[CurrentUser] User $User): string
    {
        $location = $location;
        $user = $User->id;

        return 'r1';
    }
}
