<?php

namespace App\tests;

use App\models\HealingItem;
use App\models\Inventory;

class BakeryTest extends BaseTest
{
    public function test_can_get_bakery()
    {
        $this->get('/handlers/handler_v.php?building=bakery');
        $this->assertEquals(200, $this->response->statusCode);
    }

    public function test_retrieve_store_items()
    {
        $this->get('/api/bakery/store');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    public function test_buy_bakery_item()
    {
        $HealingItem = HealingItem::where('bakery_item', 1)->inRandomOrder()->limit(1)->first();

        $RequiredItems = $HealingItem->requiredItems;

        $amount = 5;

        foreach ($RequiredItems as $key => $RequiredItem) {
            Inventory::upsert([
                'username' => self::$username,
                'item' => $RequiredItem->required_item,
                'amount' => $RequiredItem->amount * $amount,
            ], ['username', 'item']);
        }

        $this->post('/api/bakery/make', [
            'item' => $HealingItem->item,
            'amount' => $amount,
        ]);

        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }
}
