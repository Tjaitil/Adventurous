<?php

namespace Tests\Feature\Conversation\ServerEvents;

use App\Conversation\ServerEvents\KapysHandler;
use App\Enums\GameLocations;
use Tests\TestCase;

final class KapysHandlerTest extends TestCase
{
    public KapysHandler $kapysHandler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->kapysHandler = app()->make(KapysHandler::class);
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

    public function test_buy_permits_when_user_has_enough_gold(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = GameLocations::GOLBAK_LOCATION->value;
        $this->insertItemToInventory($User, 'gold', 10000);

        $result = $this->kapysHandler->buyPermits(GameLocations::GOLBAK_LOCATION->value, $User);
        $this->assertTrue($result);
    }

    public function test_buy_permits_when_user_does_not_have_enough_gold(): void
    {
        $User = $this->getRandomUser();
        $User->player->location = GameLocations::GOLBAK_LOCATION->value;

        $this->insertItemToInventory($User, 'gold', 0);
        $result = $this->kapysHandler->buyPermits(GameLocations::GOLBAK_LOCATION->value, $User);
        $this->assertFalse($result);
    }
}
