<?php

namespace App\tests;

class ExampleTest extends BaseTest
{
    public function test_building_handler()
    {
        $buildings = [
            'armycamp',
            'stockpile',
            'merchant',
            'crops',
            'miner',
            'citycentre',
            'armory',
            'armymission',
            'archeryshop',
            'smithy',
        ];

        foreach ($buildings as $key => $value) {
            $response = $this->get('/handlers/handler_v.php?building=' . $value);
            $this->assertTrue($response->getStatusCode() === 200);
        }
    }
}
