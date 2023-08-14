<?php

namespace App\tests;

use App\models\HealingItem;
use App\models\Hunger;
use App\models\Inventory;

class HungerTest extends BaseTest
{
    public function test_get_hunger()
    {
        $this->get('/api/hunger/get');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }


    public function test_get_heal_data()
    {

        $HealingItem = HealingItem::first();

        $this->get('/api/hunger/item/get', [
            'item' => $HealingItem->item
        ]);

        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }



    public function test_max_hunger()
    {
        $HealingItem = HealingItem::first();

        Inventory::upsert([
            'username' => self::$username,
            'item' => $HealingItem->item,
            'amount' => 3,
        ], ['username', 'item']);

        $Hunger = Hunger::where('user_id', self::$user_id)->first();

        $Hunger->current = 100;

        $this->post('/api/hunger/restore', [
            'item' => $HealingItem->item,
            'amount' => 1,
        ]);

        $this->assertEquals(422, $this->response->statusCode);
    }



    public function test_restore_hunger()
    {
        $HealingItem = HealingItem::first();

        Inventory::upsert([
            'username' => self::$username,
            'item' => $HealingItem->item,
            'amount' => 3,
        ], ['username', 'item']);

        $Hunger = Hunger::where('user_id', self::$user_id)->first();
        $Hunger->current = 80;
        $Hunger->save();

        $this->post('/api/hunger/restore', [
            'item' => $HealingItem->item,
            'amount' => 1,
        ]);

        $this->assertEquals(200, $this->response->statusCode);
    }



    public function test_restore_hunger_with_invalid_item()
    {
        $this->post('/api/hunger/restore', [
            'item' => "invalid item",
            'amount' => 3,
        ]);

        $this->assertEquals(422, $this->response->statusCode);
    }
}
