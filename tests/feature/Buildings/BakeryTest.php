<?php

namespace App\tests;

use App\Models\HealingItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BakeryTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    public function test_can_get_bakery()
    {
        $this->actingAs($this->RandomUser)
            ->get('/bakery')
            ->assertStatus(200);
    }

    public function test_retrieve_store_items()
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/bakery/store');

        $response->assertStatus(200);
        $response->json();
    }

    /**
     * @group store-purchase
     */
    public function test_buy_bakery_item()
    {
        $HealingItem = HealingItem::where('bakery_item', 1)->inRandomOrder()->limit(1)->first();

        $RequiredItems = $HealingItem->requiredItems;

        $amount = 5;

        foreach ($RequiredItems as $key => $RequiredItem) {
            $this->insertItemToInventory($this->RandomUser, $RequiredItem->required_item, ($RequiredItem->amount * $amount) + 2);
        }

        $this->insertCurrencyToInventory($this->RandomUser, $HealingItem->price * $amount);

        $response = $this->actingAs($this->RandomUser)->post('/bakery/make', [
            'item' => $HealingItem->item,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseHas('inventory', [
            'username' => $this->RandomUser->username,
            'item' => $HealingItem->item,
            'amount' => $amount,
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
