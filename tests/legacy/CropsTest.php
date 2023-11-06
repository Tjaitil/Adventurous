<?php

namespace App\tests;

use App\tests\support\DatabaseUtils\UserTrait;
use App\Enums\GameLocations;
use App\Models\Farmer;
use App\tests\support\DatabaseUtils\InventoryTrait;
use Carbon\Carbon;

class CropsTest extends BaseTest
{
    use UserTrait, InventoryTrait;

    public static function locationProvider()
    {
        return [
            [GameLocations::TOWHAR_LOCATION->value, 'tomato'],
        ];
    }

    public function test_retrieve_building()
    {
        $this->get('/handlers/handler_v.php?building=crops');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertIsString($this->response->body);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_get_countdown(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location);

        $this->get('/api/crops/countdown');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_get_view_data(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location);

        $this->get('/api/crops/data');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_start_growing(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location);
        $this->insertInventoryItem($cropType . ' seed', 2);

        Farmer::where('username', self::$username)->update([
            'crop_type' => null,
        ]);

        $this->post('/api/crops/start', [
            'crop_type' => $cropType,
            'workforce_amount' => 1
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_harvest(string $location, string $cropType)
    {
        $this->setUserCurrentLocation($location);

        Farmer::where('username', self::$username)->update([
            'crop_type' => $cropType,
            'crop_finishes_at' => Carbon::now()->subMinutes(1),
        ]);

        $this->post('/api/crops/end', [
            'crop_type' => $cropType,
            'workforce_amount' => 1,
            'is_cancelling' => false
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }
}
