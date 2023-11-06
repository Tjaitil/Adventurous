<?php

namespace App\tests;

use App\tests\support\DatabaseUtils\InventoryTrait;
use App\tests\support\DatabaseUtils\UserTrait;

class WorkforceLodgeTest extends BaseTest
{

    use InventoryTrait, UserTrait;

    function test_retrieve_building()
    {
        $this->get('/handlers/handler_v.php?building=workforcelodge');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertIsString($this->response->body);
    }

    function test_upgrade_efficiency()
    {
        $this->insertInventoryItem('gold', 100000);

        $this->post('/api/workforcelodge/efficiency/upgrade', [
            'type' => 'farmer',
        ]);

        $this->assertEquals(200, $this->response->statusCode);
    }
}
