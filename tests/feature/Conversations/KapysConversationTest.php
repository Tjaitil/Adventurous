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
                function () {
                    Inventory::where('username', $this->RandomUser->username)->where('item', 'gold')
                        ->update(['amount' => 0]);
                },
                function () {
                    Inventory::where('username', $this->RandomUser->username)->where('item', 'gold')
                        ->update(['amount' => 10000]);
                },
            ];

    }

    public function test_conversation_tree(): void
    {
        $this->triggerConversationTree('kapys');
    }
}
