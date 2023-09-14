<?php

namespace App\tests;

use App\enums\GameLocations;
use App\models\Miner;
use App\tests\support\DatabaseUtils\UserTrait;
use Carbon\Carbon;

class MinerTest extends BaseTest
{
    use UserTrait;

    /**
     * 
     * @return array<int, array<string, string>> 
     */
    public static function locationProvider()
    {
        return [
            [GameLocations::GOLBAK_LOCATION->value, 'iron ore'],
        ];
    }

    public function test_retrieve_building()
    {
        $this->get('/handlers/handler_v.php?building=mine');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertIsString($this->response->body);
    }

    public function test_get_countdown()
    {
        $this->get('/api/mine/countdown');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    public function test_get_view_data()
    {
        $this->get('/api/mine/data');
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_start_mining(string $location, string $mineralType)
    {
        $this->setUserCurrentLocation($location);

        $this->post('/api/mine/start', [
            'mineral_ore' => $mineralType,
            'workforce_amount' => 1
        ]);
        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }

    /**
     * 
     * @dataProvider locationProvider
     */
    public function test_end_mining(string $location, string $mineralType)
    {
        $this->setUserCurrentLocation($location);

        Miner::where('username', self::$username)->update([
            'mineral_type' => $mineralType,
            'mining_finishes_at' => Carbon::now()->subMinutes(1)
        ]);

        $this->post('/api/mine/end', [
            'mineral_ore' => 'iron ore',
            'is_cancelling' => false
        ]);

        $this->assertEquals(200, $this->response->statusCode);
        $this->assertJson($this->response->body);
    }
}
