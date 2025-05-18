<?php

namespace Tests\Feature\Conversation\Handlers;

use App\Conversation\Handlers\PesrHandler;
use Tests\ConversationTestCase;

class PesrHandlerTest extends ConversationTestCase
{
    public PesrHandler $pesrHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pesrHandler = app()->make(PesrHandler::class);
    }

    public function test_location_conditional_returns_true_when_user_is_not_in_location(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = 'snerpiir';
        $result = $this->pesrHandler->currentLocationConditional('snerpiir', $User);
        $this->assertTrue($result);
    }

    public function test_location_conditional_returns_false_when_user_is_in_location(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = 'snerpiir';
        $result = $this->pesrHandler->currentLocationConditional('golbak', $User);
        $this->assertFalse($result);
    }
}
