<?php

namespace Tests\Feature\Buildings;

use App\Models\SmithyItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SmithyTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
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
        $response->assertJsonStructure(([
            'data' => [
                'store_items',
            ],
        ]));
    }

    /**
     * @group store-purchase
     */
    public function test_can_smith_item(): void
    {
        $SmithyItem = SmithyItem::inRandomOrder()->limit(1)->first();
        if (! $SmithyItem instanceof SmithyItem) {
            $this->fail('No SmithyItem found');
        }

        $RequiredItems = $SmithyItem->requiredItems;

        foreach ($SmithyItem->skillRequirements as $key => $skillRequirement) {
            if ($skillRequirement->skill === 'miner') {
                $this->setMinerLevel($skillRequirement->level);
            }
        }

        $amount = 5;

        foreach ($RequiredItems as $key => $RequiredItem) {
            $this->insertItemToInventory($this->RandomUser, $RequiredItem->required_item, ($RequiredItem->amount * $amount) + 2);
        }

        $this->insertCurrencyToInventory($this->RandomUser, $SmithyItem->store_value * $amount);

        $response = $this->post('/smithy/smith', [
            'item' => $SmithyItem->item,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseHas('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => $SmithyItem->item,
            'amount' => $amount * $SmithyItem->item_multiplier,
        ]);

        foreach ($RequiredItems as $key => $RequiredItem) {
            $this->assertDatabaseHas('inventory', [
                'user_id' => $this->RandomUser->id,
                'item' => $RequiredItem->required_item,
                'amount' => 2,
            ]);
        }
    }
}
