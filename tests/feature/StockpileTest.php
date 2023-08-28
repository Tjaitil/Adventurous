<?php

namespace Tests\Feature;

use App\models\Inventory;
use App\models\Stockpile;
use App\tests\BaseTest;

class StockpileTest extends BaseTest
{
    public function test_retrieve_stockpile()
    {
        $this->get('/handlers/handler_v.php?building=travelbureau');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertIsString($this->response->body);
    }

    public function test_insert_item()
    {
        $InventoryItem = Inventory::where('username', self::$username)->inRandomOrder()->limit(1)->first();

        $this->post('/api/stockpile/update', [
            'item' => $InventoryItem->item,
            'amount' => 1,
            'insert' => true,
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    public function test_insert_item_with_invalid_amount()
    {
        $InventoryItem = Inventory::where('username', self::$username)->inRandomOrder()->limit(1)->first();

        $this->post('/api/stockpile/update', [
            'item' => $InventoryItem->item,
            'amount' => $InventoryItem->amount + 1,
            'insert' => true,
        ]);
        $this->assertEquals(400, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    public function test_withdraw_item()
    {
        $StockpileItem = Stockpile::where('username', self::$username)->inRandomOrder()->limit(1)->first();

        $this->post('/api/stockpile/update', [
            'item' => $StockpileItem->item,
            'amount' => 1,
            'insert' => false,
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    public function test_withdraw_item_with_invalid_amount()
    {
        $StockpileItem = Stockpile::where('username', self::$username)->inRandomOrder()->limit(1)->first();

        $this->post('/api/stockpile/update', [
            'item' => $StockpileItem->item,
            'amount' => $StockpileItem->amount + 1,
            'insert' => false,
        ]);
        $this->assertEquals(400, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }
}
