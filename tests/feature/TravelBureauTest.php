<?php

namespace App\tests;

use App\models\Inventory;
use App\models\TravelBureauCart;

class TravelBureauTest extends BaseTest
{

    function test_retrieve_building()
    {
        $this->get('/handlers/handler_v.php?building=travelbureau');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertIsString($this->response->body);
    }

    function test_retrieve_store_items()
    {
        $this->get('/api/travelbureau/store');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    function test_buy_item()
    {
        // TODO: Set this to not be a hardcoded cart
        $required_items = TravelBureauCart::where('name', 'steel cart')->with('requiredItems')->first()->requiredItems;

        foreach ($required_items as $key => $required_item) {
            $test = Inventory::upsert(
                [
                    'username' => self::$username,
                    'item' => $required_item->required_item,
                    'amount' => $required_item->amount,
                ],
                ['username', 'item']
            );
        }

        $this->post('/api/travelbureau/buy', [
            'item' => "steel cart",
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }
}
