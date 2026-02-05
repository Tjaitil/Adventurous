<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\ConversationTestCase;
use Tests\Support\UserTrait;
use Tests\Utils\Contracts\ConversationContract;

class PesrConversationTest extends ConversationTestCase implements ConversationContract
{
    use RefreshDatabase, UserTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->person = 'pesr';
        $this->loadConversationFile();
        $this->actingAs($this->getRandomUser());
    }

    public function test_conversation_tree(): void
    {
        $this->check_conversation_structure($this->conversation_file['index']);
    }

    public function test_callables_exists(): void
    {
        $this->check_handler_callables($this->app->make(\App\Conversation\Handlers\PesrHandler::class));
    }

    #[TestWith(['golbak'])]
    #[TestWith(['khanz'])]
    #[TestWith(['krasnur'])]
    #[TestWith(['tasnobil'])]
    #[TestWith(['fagna'])]
    #[TestWith(['snerpiir'])]
    #[TestWith(['cruendo'])]
    #[TestWith(['ter'])]
    #[TestWith(['towhar'])]
    public function test_prrr_include_client_callback(string $location): void
    {
        $this->setUserCurrentLocation($location, $this->getRandomUser());
        $this->setConversationIndex('prr');

        $result = $this->callNext(0);

        foreach ($result['conversation_segment']['options'] as $key => $value) {
            $this->assertEquals('GameTravelCallback', $value['client_callback']);

            $this->assertEquals('end', $value['next_key']);
        }
    }
}
