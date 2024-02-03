<?php

namespace App\tests;

use App\Models\FarmerWorkforce;
use App\Models\LevelData;
use App\Models\MinerWorkforce;
use App\Models\UserLevels;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorkforceLodgeTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
    }

    public function test_retrieve_building(): void
    {
        $response = $this->actingAs($this->RandomUser)
            ->get('/workforcelodge');

        $response->assertStatus(200);
    }

    /**
     * @dataProvider profiencyProvider
     */
    public function test_upgrade_efficiency(string $profiency, int $level): void
    {
        $this->insertCurrencyToInventory($this->RandomUser, 100000);
        $LevelData = LevelData::where('max_efficiency_level', $level)
            ->first();

        $LevelDataUnder = LevelData::where('max_efficiency_level', $level - 1)
            ->first();

        if ($profiency === 'farmer') {
            UserLevels::where('username', $this->RandomUser->username)
                ->update(['farmer_level' => $LevelData->level]);

            FarmerWorkforce::where('username', $this->RandomUser->username)
                ->update(['efficiency_level' => $LevelDataUnder->max_efficiency_level]);
        } else {
            UserLevels::where('username', $this->RandomUser->username)
                ->update(['miner_level' => $LevelData->level]);

            MinerWorkforce::where('username', $this->RandomUser->username)
                ->update(['efficiency_level' => $LevelDataUnder->max_efficiency_level]);
        }

        $response = $this->actingAs($this->RandomUser)
            ->post('/workforcelodge/efficiency/upgrade', [
                'skill' => $profiency,
            ]);

        $response->assertStatus(200);

        $response->json();
    }

    /**
     * @dataProvider profiencyProvider
     */
    public function test_cannot_upgrade_efficiency_when_reached_max_efficiency_level(string $profiency, int $level): void
    {
        $this->insertCurrencyToInventory($this->RandomUser, 100000);

        $LevelData = LevelData::where('max_efficiency_level', $level)->first();

        if ($profiency === 'farmer') {
            UserLevels::where('username', $this->RandomUser->username)
                ->update(['farmer_level' => $LevelData->level]);

            FarmerWorkforce::where('username', $this->RandomUser->username)
                ->update(['efficiency_level' => $LevelData->max_efficiency_level]);
        } else {
            UserLevels::where('username', $this->RandomUser->username)
                ->update(['miner_level' => $LevelData->level]);

            MinerWorkforce::where('username', $this->RandomUser->username)
                ->update(['efficiency_level' => $LevelData->max_efficiency_level]);
        }

        $response = $this->actingAs($this->RandomUser)
            ->post('/workforcelodge/efficiency/upgrade', [
                'skill' => $profiency,
            ]);

        $response->json();
        $response->assertStatus(422);
    }

    public static function profiencyProvider(): array
    {
        return [
            'farmer' => ['profiency' => 'farmer', 'efficiency_level' => 2],
            'miner' => ['profiency' => 'miner', 'efficiency_level' => 2],
        ];
    }
}
