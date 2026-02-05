<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\ConversationTestCase;
use Tests\Support\UserTrait;
use Tests\Utils\Contracts\ConversationContract;

class SailorConversationTest extends ConversationTestCase implements ConversationContract
{
    use RefreshDatabase, UserTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->person = 'sailor';
        $this->loadConversationFile();
        $this->actingAs($this->getRandomUser());
    }

    public function test_conversation_tree(): void
    {
        $this->check_conversation_structure($this->conversation_file['index']);
    }

    public function test_callables_exists(): void
    {
        $this->check_handler_callables($this->app->make(\App\Conversation\Handlers\SailorHandler::class));
    }

    #[TestWith(['towhar'])]
    #[TestWith(['pvitul'])]
    #[TestWith(['hirtam'])]
    #[TestWith(['krasnur'])]
    #[TestWith(['fagna'])]
    #[TestWith(['cruendo'])]
    #[TestWith(['towhar'])]
    public function test_prrr_include_client_callback_and_current_location_is_excluded(string $location): void
    {
        $this->setUserCurrentLocation($location, $this->getRandomUser());
        $this->setConversationIndex('slr');

        $result = $this->callNext(0);

        $locations = array_map(
            fn ($option) => $option['option_values']['location'] ?? null,
            $result['conversation_segment']['options']
        );

        $this->assertNotContains($location, $locations);

        foreach ($result['conversation_segment']['options'] as $key => $value) {
            $this->assertEquals('GameTravelCallback', $value['client_callback']);

            $this->assertEquals('end', $value['next_key']);
        }
    }
}
