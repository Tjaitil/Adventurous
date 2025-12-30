<?php

namespace App\tests;

use App\Enums\SkillNames;
use App\Models\EfficiencyUpgrade;
use App\Models\FarmerWorkforce;
use App\Models\LevelData;
use App\Models\MinerWorkforce;
use App\Models\UserLevels;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WorkforceLodgeTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['testing'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->actingAs($this->RandomUser);
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
    public function test_upgrade_efficiency(string $profiency, int $efficiency_level): void
    {
        $this->insertCurrencyToInventory($this->RandomUser, 100000);
        $LevelData = LevelData::where('max_efficiency_level', $efficiency_level)
            ->first();

        $efficiencyLevelToUse = $efficiency_level - 1;

        $LevelDataUnder = LevelData::where('max_efficiency_level', $efficiencyLevelToUse)
            ->first();

        if ($profiency === 'farmer') {
            UserLevels::where('user_id', $this->RandomUser->id)
                ->update(['farmer_level' => $LevelData->level]);

            FarmerWorkforce::where('user_id', $this->RandomUser->id)
                ->update(['efficiency_level' => $LevelDataUnder->max_efficiency_level]);
        } else {
            UserLevels::where('user_id', $this->RandomUser->id)
                ->update(['miner_level' => $LevelData->level]);

            MinerWorkforce::where('user_id', $this->RandomUser->id)
                ->update(['efficiency_level' => $LevelDataUnder->max_efficiency_level]);
        }

        $cost = EfficiencyUpgrade::where('level', $efficiencyLevelToUse)->first()->price;

        $response = $this->post('/workforcelodge/efficiency/upgrade', [
            'skill' => $profiency,
        ]);

        $response->assertStatus(200);

        $response->json();

        $response->assertJsonStructure(['data' => [
            'efficiency_level',
            'new_efficiency_price',
        ]]);

        $this->assertDatabaseHas('inventory', [
            'user_id' => $this->RandomUser->id,
            'item' => config('adventurous.currency'),
            'amount' => 100000 - $cost,
        ]);
    }

    /**
     * @dataProvider profiencyProvider
     */
    public function test_cannot_upgrade_efficiency_when_reached_max_efficiency_level(string $profiency, int $efficiency_level): void
    {
        $this->insertCurrencyToInventory($this->RandomUser, 100000);

        $LevelData = LevelData::where('max_efficiency_level', $efficiency_level)->first();

        if ($profiency === 'farmer') {
            UserLevels::where('user_id', $this->RandomUser->id)
                ->update(['farmer_level' => $LevelData->level]);

            FarmerWorkforce::where('user_id', $this->RandomUser->id)
                ->update(['efficiency_level' => $LevelData->max_efficiency_level]);
        } else {
            UserLevels::where('user_id', $this->RandomUser->id)
                ->update(['miner_level' => $LevelData->level]);

            MinerWorkforce::where('user_id', $this->RandomUser->id)
                ->update(['efficiency_level' => $LevelData->max_efficiency_level]);
        }

        $response = $this->post('/workforcelodge/efficiency/upgrade', [
            'skill' => $profiency,
        ]);

        $response->json();
        $response->assertStatus(422);
    }

    public static function profiencyProvider(): array
    {
        return [
            'farmer' => ['profiency' => SkillNames::FARMER->value, 'efficiency_level' => 2],
            'miner' => ['profiency' => SkillNames::MINER->value, 'efficiency_level' => 2],
        ];
    }
}
