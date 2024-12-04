<?php

namespace Tests\Feature\Conversations;

use App\Models\Inventory;
use Tests\TestCase;
use Tests\Utils\Contracts\ConversationContract;
use Tests\Utils\Traits\ConversationTester;

class KapysConversationTest extends TestCase implements ConversationContract
{
    use ConversationTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->__constructConversationTester('kapys');
        $this->actingAs($this->getRandomUser());

        $this->hasServerEventTestCallbacks['kpsQrrS'] =
            [
                fn () => false,
                function () {
                    Inventory::upsert(['amount' => 10000, 'item' => 'gold', 'user_id' => $this->RandomUser->id], ['item', 'user_id']);
                },
            ];

    }

    public function test_conversation_tree(): void
    {
        $this->triggerConversationTree('kapys');
    }
}
