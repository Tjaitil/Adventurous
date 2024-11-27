<?php

namespace Tests\Feature;

use App\Enums\WorldChangeType;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorldLoaderTest extends TestCase
{
    use DatabaseTransactions;

    public $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    /**
     * @group route
     */
    public function test_route_returns_successfull_and_json(): void
    {
        $User = User::find(1);
        $response = $this->actingAs($User)->get('/worldloader');

        $response->json();
        $response->assertStatus(200);
    }

    public static function getDestination()
    {

        return [
            'towhar' => ['destination' => 'towhar'],
            'golbak' => ['destination' => 'golbak'],
            'krasnur' => ['destination' => 'krasnur'],
            'ter' => ['destination' => 'ter'],
            'fansalplains' => ['destination' => 'fansalplains'],
            'cruendo' => ['destination' => 'cruendo'],
            'snerpiir' => ['destination' => 'snerpiir'],
            'fagna' => ['destination' => 'fagna'],
            'tasnobil' => ['destination' => 'tasnobil'],
            'khanz' => ['destination' => 'khanz'],
        ];
    }

    /**
     * @dataProvider getDestination
     */
    public function test_change_map_with_new_destination(string $destination): void
    {
        $User = User::find(1);
        $response = $this->actingAs($User)->post('/worldloader/change', [
            'method' => WorldChangeType::TRAVEL->value,
            'new_destination' => $destination,
        ]);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('current_map', $json['data']);
    }

    public function test_change_map_with_coordinates(): void
    {
        $User = User::find(1);
        $response = $this->actingAs($User)->post('/worldloader/change', [
            'method' => WorldChangeType::NEXT_MAP->value,
            'new_map' => [
                'newX' => -1,
                'newY' => 0,
            ],
        ]);

        $response->assertStatus(200);
        $response->json();
    }

    /**
     * @dataProvider combatMapProvider
     */
    public function test_respawn_after_death(string $map, string $result): void
    {
        $User = User::find(1);
        $UserData = UserData::where('username', $User->username)->first();
        $UserData->update([
            'map_location' => $map,
        ]);

        $response = $this->actingAs($User)->post('/worldloader/change', [
            'method' => WorldChangeType::RESPAWN->value,
        ]);

        $json = $response->json();
        $this->assertEquals($result, $json['data']['current_map']);
        $this->assertEquals($UserData->refresh()->map_location, $result);
        $response->assertStatus(200);
    }

    public static function combatMapProvider()
    {
        return [
            '4.2' => ['map' => '4.2', 'result' => '4.3'],
            '6.2' => ['map' => '6.2', 'result' => '5.2'],
            '8.3' => ['map' => '8.3', 'result' => '8.2'],
            '3.10' => ['map' => '3.10', 'result' => '4.9'],
        ];
    }
}
