<?php

namespace Tests\Feature\Conversation\Handlers;

use App\Conversation\Handlers\KapysHandler;
use App\Enums\GameLocations;
use App\Models\Miner;
use App\Models\MinerPermitCost;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\ConversationTestCase;

final class KapysHandlerTest extends ConversationTestCase
{
    public KapysHandler $kapysHandler;

    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->kapysHandler = app()->make(KapysHandler::class);
    }

    #[DataProvider('minerLocationProvider')]
    public function test_price_replacer_gets_correct_price(string $location)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $result = $this->kapysHandler->priceReplacer($this->RandomUser);

        $MinerPermitCost = MinerPermitCost::where('location', $location)->firstOrFail();

        $this->assertEquals($MinerPermitCost->permit_cost, $result);
    }

    public function test_location_conditional_returns_true(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = GameLocations::GOLBAK_LOCATION->value;
        $result = $this->kapysHandler->currentLocationConditional(GameLocations::GOLBAK_LOCATION->value, $User);
        $this->assertTrue($result);
    }

    public function test_location_conditional_returns_false(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = GameLocations::GOLBAK_LOCATION->value;
        $result = $this->kapysHandler->currentLocationConditional(GameLocations::KHANZ_LOCATION->value, $User);
        $this->assertFalse($result);
    }

    public static function minerLocationProvider()
    {
        return [
            'snerpiir' => [GameLocations::SNERPIIR_LOCATION->value],
            'golbak' => [GameLocations::GOLBAK_LOCATION->value],
        ];
    }

    #[DataProvider('minerLocationProvider')]
    public function test_buy_permits_when_user_has_enough_gold(string $location): void
    {
        $User = $this->getRandomUser();
        $this->setUserCurrentLocation($location, $User);
        $this->insertItemToInventory($User, 'gold', 10000);

        $Miner = Miner::where('user_id', $this->RandomUser->id)
            ->where('location', $location)
            ->first();

        $MinerPermitCost = MinerPermitCost::where('location', $location)->first();

        $result = $this->kapysHandler->buyPermits($location, $User);

        $this->assertDatabaseHas('miner', [
            'user_id' => $this->RandomUser->id,
            'location' => $location,
            'permits' => $Miner->permits + $MinerPermitCost->permit_amount,
        ]);

        $this->assertEquals('r0', $result);
    }

    public function test_buy_permits_when_user_does_not_have_enough_gold(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = GameLocations::GOLBAK_LOCATION->value;

        $this->insertItemToInventory($User, 'gold', 0);
        $result = $this->kapysHandler->buyPermits(GameLocations::GOLBAK_LOCATION->value, $User);
        $this->assertEquals('r1', $result);
    }
}
