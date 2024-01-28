<?php

namespace App\tests;

use App\Models\TravelBureauCart;
use App\Models\UserLevels;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TravelBureauTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

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

    public function test_buy_item()
    {
        $cart = TravelBureauCart::where('name', 'steel cart')->with('requiredItems')->first();

        $skillRequirement = $cart->skillRequirements->first();

        UserLevels::where('username', $this->RandomUser->username)->update(['trader_level' => $skillRequirement->level]);

        foreach ($cart->requiredItems as $key => $required_item) {
            $this->insertItemToInventory($this->RandomUser->username, $required_item->required_item, $required_item->amount);
        }
        $this->insertCurrencyToInventory($this->RandomUser->username, $cart->store_value);

        $response = $this->actingAs($this->RandomUser)->post('/travelbureau/buy', [
            'item' => 'steel cart',
        ]);
        $response->assertStatus(200);

        $response->json();
    }

    /**
     * Note: Never got this function to work with this->expectException()
     */
    public function test_json_exception_is_thrown_when_user_levels_cannot_be_found()
    {
        $cart = TravelBureauCart::with(['requiredItems', 'skillRequirements'])->where('name', 'frajrite cart')
            ->first();

        foreach ($cart->requiredItems as $key => $required_item) {
            $this->insertItemToInventory($this->RandomUser->username, $required_item->required_item, $required_item->amount);
        }
        $this->insertCurrencyToInventory($this->RandomUser->username, $cart->store_value);

        UserLevels::where('username', $this->RandomUser->username)->update(['username' => 'foo']);

        $response = $this->withExceptionHandling()->actingAs($this->RandomUser)
            ->post('/travelbureau/buy', [
                'item' => 'frajrite cart',
            ]);
        $response->assertStatus(500);

        $response->json();
    }
}
