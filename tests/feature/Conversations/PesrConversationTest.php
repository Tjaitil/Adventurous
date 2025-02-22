<?php

namespace Tests\Feature;

use Tests\Support\UserTrait;
use Tests\TestCase;
use Tests\Utils\Contracts\ConversationContract;
use Tests\Utils\Traits\ConversationTester;

class PesrConversationTest extends TestCase implements ConversationContract
{
    use ConversationTester, UserTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->getRandomUser());
        $this->__constructConversationTester('pesr');
    }

    public function test_conversation_tree(): void
    {
        $this->triggerConversationTree('pesr');
    }
}
