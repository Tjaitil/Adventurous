<?php

namespace Tests\Feature\Buildings;

use App\Models\Inventory;
use App\Models\Stockpile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StockpileTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    public function test_stockpile_init_route_renders(): void
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/stockpile');

        $response->assertStatus(200);
    }

    public function test_insert_item()
    {
        $InventoryItem = Inventory::where('username', $this->RandomUser->username)
            ->inRandomOrder()->limit(1)->first();
        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $InventoryItem->item,
                'amount' => 1,
                'insert' => true,
            ]);
        $response->json();
        $response->assertStatus(200);
    }

    public function test_insert_item_with_invalid_amount()
    {
        $InventoryItem = Inventory::where('username', $this->RandomUser->username)->inRandomOrder()->limit(1)->first();

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $InventoryItem->item,
                'amount' => $InventoryItem->amount + 1,
                'insert' => true,
            ]);
        $response->json();
        $response->assertStatus(400);
    }

    public function test_withdraw_item()
    {
        $StockpileItem = Stockpile::where('username', $this->RandomUser->username)->inRandomOrder()->limit(1)->first();

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $StockpileItem->item,
                'amount' => 1,
                'insert' => false,
            ]);
        $response->json();
        $response->assertStatus(200);
    }

    public function test_withdraw_item_with_invalid_amount()
    {
        $StockpileItem = Stockpile::where('username', $this->RandomUser->username)->inRandomOrder()->limit(1)->first();

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $StockpileItem->item,
                'amount' => $StockpileItem->amount + 1,
                'insert' => false,
            ]);

        $response->json();
        $response->assertStatus(400);
    }
}
