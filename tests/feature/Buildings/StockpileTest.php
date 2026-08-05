<?php

namespace Tests\Feature\Buildings;

use App\Models\Item;
use App\Models\Stockpile;
use App\Models\UserData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockpileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_stockpile_data_route_returns_expected_shape(): void
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/stockpile/data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['items'],
                'meta' => ['max_slots'],
            ]);
    }

    public function test_insert_item_returns_data_and_updates_inventory_and_stockpile(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 1);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 1,
                'insert' => true,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['items'],
                'meta' => ['max_slots'],
            ])
            ->assertJsonMissingPath('html.stockpile');

        $this->assertDatabaseHas('stockpile', [
            'user_id' => $this->RandomUser->id,
            'item_id' => $Item->item_id,
            'amount' => 1,
        ]);
        $this->assertDatabaseMissing('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => $Item->name,
        ]);
    }

    public function test_insert_all_of_item(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 5);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 5,
                'insert' => true,
            ]);

        $response->assertStatus(200)
            ->assertJsonMissingPath('html.stockpile');

        $this->assertDatabaseHas('stockpile', [
            'user_id' => $this->RandomUser->id,
            'item_id' => $Item->item_id,
            'amount' => 5,
        ]);
    }

    public function test_cannot_withdraw_when_inventory_is_full(): void
    {
        $Items = Item::query()->inRandomOrder()->limit(18)->get();

        foreach ($Items as $key => $value) {
            $this->insertItemToInventory($this->RandomUser, $value->name, 5);
        }

        Stockpile::query()->insert([
            'user_id' => $this->RandomUser->id,
            'item_id' => $Items->firstOrFail()->item_id,
            'amount' => 1,
        ]);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Items->first()->name,
                'amount' => 1,
                'insert' => false,
            ]);

        $response->assertStatus(422);

    }

    public function test_insert_item_with_invalid_amount(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();
        $this->insertItemToInventory($this->RandomUser, $Item->name, 1);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 5,
                'insert' => true,
            ]);

        $response->assertStatus(400);
    }

    public function test_withdraw_item_returns_data_and_updates_inventory_and_stockpile(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();
        Stockpile::query()->insert([
            'user_id' => $this->RandomUser->id,
            'item_id' => $Item->item_id,
            'amount' => 1,
        ]);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 1,
                'insert' => false,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['items'],
                'meta' => ['max_slots'],
            ])
            ->assertJsonMissingPath('html.stockpile');

        $this->assertDatabaseMissing('stockpile', [
            'user_id' => $this->RandomUser->id,
            'item_id' => $Item->item_id,
        ]);
        $this->assertDatabaseHas('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => $Item->name,
            'amount' => 1,
        ]);
    }

    public function test_withdraw_item_with_invalid_amount(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();

        Stockpile::query()->insert([
            'user_id' => $this->RandomUser->id,
            'item_id' => $Item->item_id,
            'amount' => 1,
        ]);

        $StockpileItem = Stockpile::query()
            ->where('user_id', $this->RandomUser->id)
            ->inRandomOrder()
            ->firstOrFail();

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $StockpileItem->item->name,
                'amount' => $StockpileItem->amount + 1,
                'insert' => false,
            ]);

        $response->assertStatus(400);
    }

    public function test_insert_item_is_rejected_on_stockpile_max_amount_limit(): void
    {
        Stockpile::where('user_id', $this->RandomUser->id)->delete();

        $Items = Item::query()->inRandomOrder()->get()->take(2);

        Stockpile::query()->insert([
            'user_id' => $this->RandomUser->id,
            'item_id' => $Items->first()->item_id,
            'amount' => 1,
        ]);

        UserData::query()
            ->where('username', $this->RandomUser->username)
            ->update(['stockpile_max_amount' => 1]);

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Items->last()->name,
                'amount' => 1,
                'insert' => true,
            ]);

        $response->assertStatus(422);
    }

    public function test_withdraw_item_fails_when_item_is_missing_in_stockpile(): void
    {
        $Item = Item::query()->inRandomOrder()->firstOrFail();

        $response = $this->actingAs($this->RandomUser)
            ->post('/stockpile/update', [
                'item' => $Item->name,
                'amount' => 1,
                'insert' => false,
            ]);

        $response->assertStatus(400);

        $this->assertDatabaseMissing('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => $Item->name,
        ]);
    }
}
