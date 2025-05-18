<?php

namespace Tests\Utils\Contracts;

interface ConversationContract
{
    public function test_conversation_tree(): void;

    public function test_callables_exists(): void;
}
