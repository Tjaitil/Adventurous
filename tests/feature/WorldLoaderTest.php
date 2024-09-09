<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserData;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorldLoaderTest extends TestCase
{
    use DatabaseTransactions;

    public $connectionsToTransact = ['mysql'];

    public function setUp(): void
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
            'is_new_map_string' => true,
            'new_destination' => $destination,
        ]);

        $json = $response->json();
        $this->assertArrayHasKey('current_map', $json['data']);
        $response->assertStatus(200);
    }

    public function test_change_map_with_coordinates(): void
    {
        $User = User::find(1);
        $user_data = UserData::where('username', $User->name)->select('map_location')->first();
        $response = $this->actingAs($User)->post('/worldloader/change', [
            'new_map' => [
                'newX' => -1,
                'newY' => 0,
            ],
        ]);

        $response->assertStatus(200);
        $response->json();
    }
}
