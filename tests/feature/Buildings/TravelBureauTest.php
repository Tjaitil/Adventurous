<?php

namespace App\tests;

use App\Models\TravelBureauCart;
use App\Models\UserLevels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelBureauTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();    }

    public function test_retrieve_building()
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/travelbureau');

        $response->assertStatus(200);
    }

    public function test_retrieve_store_items()
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/travelbureau/store');

        $response->json();
        $response->assertStatus(200);
    }

    /**
     * @group store-purchase
     */
    public function test_buy_item()
    {
        $cart = TravelBureauCart::where('name', 'steel cart')->with('requiredItems')->first();

        $skillRequirement = $cart->skillRequirements->first();

        UserLevels::where('user_id', $this->RandomUser->id)->update(['trader_level' => $skillRequirement->level]);
        foreach ($cart->requiredItems as $key => $required_item) {
            $this->insertItemToInventory($this->RandomUser, $required_item->required_item, $required_item->amount);
        }
        $this->insertCurrencyToInventory($this->RandomUser, $cart->store_value);

        $response = $this->actingAs($this->RandomUser)->post('/travelbureau/buy', [
            'item' => 'steel cart',
        ]);
        $response->assertStatus(200);

        $this->assertDatabaseMissing('inventory', [
            'username' => $this->RandomUser->username,
            'item' => 'steel cart',
            'amount' => 1,
        ]);

        $response->json();
    }
}
