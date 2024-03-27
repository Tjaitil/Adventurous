<?php

namespace Tests\Feature\Buildings;

use App\Models\ArcheryShopItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ArcheryShopTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->RandomUser);

        $this->beginDatabaseTransaction();
    }

    public function test_smithy_route(): void
    {
        $response = $this->get('/smithy');

        $response->assertStatus(200);
    }

    public function test_can_get_store(): void
    {
        $response = $this->get('/smithy/store');

        $response->assertStatus(200);

        $response->json();
    }

    /**
     * @group store-purchase
     */
    public function test_can_fletch_item(): void
    {
        $ArcheryShopItem = ArcheryShopItem::inRandomOrder()->limit(1)->first();
        if (! $ArcheryShopItem instanceof ArcheryShopItem) {
            $this->fail('No ArcheryShopItem found');
        }

        $RequiredItems = $ArcheryShopItem->requiredItems;

        foreach ($ArcheryShopItem->skillRequirements as $key => $skillRequirement) {
            if ($skillRequirement->skill === 'miner') {
                $this->setMinerLevel($skillRequirement->level);
            }
        }

        $amount = 5;

        foreach ($RequiredItems as $key => $RequiredItem) {
            $this->insertItemToInventory($this->RandomUser, $RequiredItem->required_item, ($RequiredItem->amount * $amount) + 2);
        }

        $this->insertCurrencyToInventory($this->RandomUser, $ArcheryShopItem->store_value * $amount);

        $response = $this->post('/archeryshop/fletch', [
            'item' => $ArcheryShopItem->item,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseHas('inventory', [
            'username' => $this->RandomUser->username,
            'item' => $ArcheryShopItem->item,
            'amount' => $amount * $ArcheryShopItem->item_multiplier,
        ]);

        foreach ($RequiredItems as $key => $RequiredItem) {
            $this->assertDatabaseHas('inventory', [
                'username' => $this->RandomUser->username,
                'item' => $RequiredItem->required_item,
                'amount' => 2,
            ]);
        }
    }
}
