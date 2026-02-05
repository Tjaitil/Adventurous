<?php

namespace Tests\Feature\Buildings;

use App\Models\Item;
use App\Models\Stockpile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockpileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();    }

    public function test_stockpile_init_route_renders(): void
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/stockpile');

        $response->assertStatus(200);
    }

    public function test_insert_item()
    {
        $Item = Item::inRandomOrder()->limit(1)->first();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 1);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 1,
                'insert' => true,
            ]);
        $response->json();
        $response->assertStatus(200);
    }

    public function test_insert_all_of_item()
    {
        $Item = Item::inRandomOrder()->limit(1)->first();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 5);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 5,
                'insert' => true,
            ]);

        $response->json();
        $response->assertStatus(200);
    }

    public function test_cannot_withdraw_when_inventory_is_full()
    {
        $Items = Item::inRandomOrder()->limit(18)->get();

        foreach ($Items as $key => $value) {
            $this->insertItemToInventory($this->RandomUser, $value->name, 5);
        }

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Items->first()->name,
                'amount' => 1,
                'insert' => false,
            ]);

        $response->json();
        $response->assertStatus(422);

    }

    public function test_insert_item_with_invalid_amount()
    {
        $Item = Item::inRandomOrder()->limit(1)->first();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 1);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 5,
                'insert' => true,
            ]);
        $response->json();
        $response->assertStatus(400);
    }

    public function test_withdraw_item()
    {
        $Item = Item::inRandomOrder()->limit(1)->first();
        Stockpile::insert([
            'username' => $this->RandomUser->username,
            'item' => $Item->name,
            'amount' => 1,
        ]);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 1,
                'insert' => false,
            ]);
        $response->json();
        $response->assertStatus(200);
    }

    public function test_withdraw_item_with_invalid_amount()
    {
        $Item = Item::inRandomOrder()->limit(1)->first();
        if (! $Item instanceof Item) {
            $this->fail('Item not found');
        }
        Stockpile::insert([
            'username' => $this->RandomUser->username,
            'item' => $Item->name,
            'amount' => 1,
        ]);

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
