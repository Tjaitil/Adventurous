<?php

namespace App\tests;

class ExampleTest extends BaseTest
{
    public function test_building_handler()
    {
        $response = $this->get('/handlers/handler_v.php');

        echo $this->assertTrue($response->getStatusCode() === 422);
    }
}
