<?php

namespace Tests\Feature\Conversations;

use App\Enums\GameEvents;
use App\Enums\GameLocations;
use App\Models\Miner;
use App\Models\MinerPermitCost;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\ConversationTest;

class KapysConversationTest extends ConversationTest
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();

        $this->person = 'kapys';
        $this->beginDatabaseTransaction();
        $this->loadConversationFile();

        $this->actingAs($this->getRandomUser());
    }

    public function test_kpsqr()
    {
        $this->setConversationIndex('kpsQ');

        $response = $this->callNext(0);
        $this->assertIsArray($response);
    }

    #[DataProvider('genericConversationDataProvider')]
    public function test_generic_conversation_segment(string $start, string $expected): void
    {
        $this->setConversationIndex($start);

        $this->callNext(0);

        $this->assertDatabaseHas('conversation_trackers', [
            'user_id' => $this->RandomUser->id,
            'current_index' => $expected,
        ]);
    }

    public static function genericConversationDataProvider()
    {
        return [
            'kpsQ' => ['start' => 'kps', 'expected' => 'kpsQ'],
            'kpsQr' => ['start' => 'kpsQ', 'expected' => 'kpsQr'],
        ];
    }

    public function test_kps_qrr_price_replacer()
    {
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

    #[DataProvider('minerLocationProvider')]
    public function test_kpsqrrs_buy_permit_with_not_enough_gold(string $location)
    {
        $this->setConversationIndex('kpsQrr', ['location' => $location]);

        $this->setUserCurrentLocation($location, $this->RandomUser);

        $this->insertCurrencyToInventory($this->RandomUser, 0);

        $Miner = Miner::where('user_id', $this->RandomUser->id)
            ->where('location', $location)
            ->first();

        $response = $this->post('conversation/next', [
            'is_starting' => false,
            'person' => $this->person,
            'selected_option' => 0,
        ]);

        $response->assertStatus(200);

        $this->assertConversationSegment($response);

        $this->assertDatabaseHas('miner', [
            'user_id' => $this->RandomUser->id,
            'location' => $location,
            'permits' => $Miner->permits,
        ]);

        $this->assertDatabaseHas('conversation_trackers', [
            'user_id' => $this->RandomUser->id,
            'current_index' => 'kpsQrrSr1',
        ]);
    }

    #[DataProvider('minerLocationProvider')]
    public function test_kpsqrrs_buy_permit_with_enough_gold(string $location)
    {
        $this->setConversationIndex('kpsQrr', ['location' => $location]);

        $this->insertCurrencyToInventory($this->RandomUser, 10000);

        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Miner = Miner::where('user_id', $this->RandomUser->id)
            ->where('location', $location)
            ->first();

        $MinerPermitCost = MinerPermitCost::where('location', $location)->first();

        $response = $this->post('conversation/next', [
            'is_starting' => false,
            'person' => $this->person,
            'selected_option' => 0,
        ]);

        $response->assertJsonStructure(([
            'conversation_segment' => [
                'index',
                'client_events',
                'options',
            ],
        ]));

        $this->assertContains(GameEvents::InventoryChangedEvent->value, $response['conversation_segment']['client_events']);

        $this->assertDatabaseHas('miner', [
            'user_id' => $this->RandomUser->id,
            'location' => $location,
            'permits' => $Miner->permits + $MinerPermitCost->permit_amount,
        ]);

        $this->assertDatabaseHas('conversation_trackers', [
            'user_id' => $this->RandomUser->id,
            'current_index' => 'kpsQrrSr0',
        ]);
    }
}
