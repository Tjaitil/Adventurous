<?php

namespace App\tests;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ZinsStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->RandomUser);
    }

    public function test_can_get_zins_store()
    {
        $this->get('/zinsstore')
            ->assertStatus(200)
            ->assertViewIs('zinsstore');
    }

    public function test_retrieve_store_items()
    {
        $response = $this->get('/zinsstore/store');

        $response->assertStatus(200);
        $response->json();
    }

    /**
     * @group store-purchase
     *
     * @dataProvider itemProvider
     */
    public function test_sell_bakery_item(string $itemName)
    {
        $Item = Item::where('name', $itemName)->first();
        $amount = 5;

        $this->insertItemToInventory($this->RandomUser, $Item->name, $amount);

        $response = $this->actingAs($this->RandomUser)->post('/zinsstore/sell', [
            'item' => $itemName,
            'amount' => $amount,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseMissing('inventory', [
            'username' => $this->RandomUser->username,
            'item' => $itemName,
        ]);
    }

    public static function itemProvider()
    {
        return [
            'daqloon horn' => ['daqloon horns'],
            'daqloon scale' => ['daqloon scale'],
        ];
    }
}
