<?php

namespace Tests\Feature\Conversations;

use App\Conversation\Handlers\KapysHandler;
use App\Enums\GameLocations;
use App\Models\ConversationTracker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\ConversationTestCase;
use Tests\Utils\Contracts\ConversationContract;

class KapysConversationTest extends ConversationTestCase implements ConversationContract
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = 'kapys';        $this->loadConversationFile();

        $this->actingAs($this->getRandomUser());
    }

    public function test_conversation_tree(): void
    {
        $this->check_conversation_structure($this->conversation_file['index']);
    }

    public function test_callables_exists(): void
    {
        $this->check_handler_callables($this->app->make(KapysHandler::class));
    }

    #[DataProvider('conditionalFalseProvider')]
    public function test_conditional_returning_false_is_not_included(string $location, string $expectMissing): void
    {
        ConversationTracker::where('user_id', $this->RandomUser->id)->firstOrFail()->update([
            'current_index' => 'kpsQ',
            'selected_option_values' => [],
        ]);

        $this->setUserCurrentLocation($location, $this->RandomUser);

        /**
         * @var \App\Services\ConversationService $conversationService
         */
        $conversationService = app()->make(\App\Services\ConversationService::class);

        $result = $conversationService->getConversation('kapys', 0, false);

        $conversationTexts = array_map(fn ($option) => $option['text'], $result['options']);

        $this->assertContains("I want to buy permits in $location mine", $conversationTexts);

        $this->assertContains('Goobye', $conversationTexts);
    }

    public static function conditionalFalseProvider(): array
    {
        return [
            'snerpiir' => ['location' => 'golbak', 'expectMissing' => 'snerpiir'],
            'golbak' => ['location' => 'snerpiir', 'expectMissing' => 'golbak'],
        ];
    }

    #[DataProvider('minerLocationProvider')]
    public function test_kps_qrr_price_replacer(string $location)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setConversationIndex('kpsQr');

        $response = $this->callNext(0);

        $this->assertStringNotContainsString(':price', $response['conversation_segment']['options'][0]['text']);
    }

    #[Group('conversation')]
    #[DataProvider('minerLocationProvider')]
    public function test_kpsqr_calls_conditional(string $location)
    {
        $this->setConversationIndex('kpsQ');

        $this->setUserCurrentLocation($location, $this->RandomUser);

        $response = $this->post('conversation/next', [
            'is_starting' => false,
            'person' => $this->person,
            'selected_option' => 0,
        ]);

        $conversationTexts = array_map(fn ($option) => $option['text'], $response['conversation_segment']['options']);

        $this->assertContains("I want to buy permits in $location mine", $conversationTexts);

        $this->assertContains('Goobye', $conversationTexts);

        $this->assertDatabaseHas('conversation_trackers', [
            'user_id' => $this->RandomUser->id,
            'current_index' => 'kpsQr',
        ]);
    }

    public static function minerLocationProvider()
    {
        return [
            'snerpiir' => [GameLocations::SNERPIIR_LOCATION->value],
            'golbak' => [GameLocations::GOLBAK_LOCATION->value],
        ];
    }
}
