<?php

namespace Tests\Feature\Conversations;

use Tests\ConversationTestCase;
use Tests\Utils\Contracts\ConversationContract;

final class ZinsConversationTest extends ConversationTestCase implements ConversationContract
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->person = 'zins';
        $this->loadConversationFile();
    }

    public function test_conversation_tree(): void
    {
        $this->check_conversation_structure($this->conversation_file['index']);
    }

    public function test_callables_exists(): void
    {
        $this->check_handler_callables($this->app->make(\App\Conversation\Handlers\ZinsHandler::class));
    }

    public function test_zrrrr1_include_client_callback(): void
    {
        $this->setConversationIndex('zrrr');

        $result = $this->callNext(0);

        $this->assertEquals('LoadZinsStoreCallback', $result['conversation_segment']['options'][0]['client_callback']);
    }
}
