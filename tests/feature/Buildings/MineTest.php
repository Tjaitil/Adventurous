<?php

namespace App\tests;

use App\Enums\GameEvents;
use App\Enums\GameLocations;
use App\Models\Miner;
use App\Models\Mineral;
use App\Models\MinerWorkforce;
use App\Models\UserLevels;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\SkillTestCase;
use Tests\Utils\Traits\ExperienceAssertions;

class MineTest extends SkillTestCase
{
    use DatabaseTransactions, ExperienceAssertions;

    public $connectionsToTransact = ['testing'];

    public MinerWorkforce $MinerWorkforce;

    protected function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();

        $this->actingAs($this->RandomUser);

        $this->MinerWorkforce = MinerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
    }

    public function test_retrieve_building()
    {
        $response = $this->get('mine');

        $response->assertStatus(200);
        $response->assertViewIs('mine');
    }

    public function test_get_countdown()
    {
        $this->setUserCurrentLocation(GameLocations::GOLBAK_LOCATION->value, $this->RandomUser);
        $response = $this->get('/mine/countdown');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'mining_finishes_at',
            'mineral_ore',
        ]);
    }

    public function test_get_view_data()
    {
        $response = $this->get('/mine/data');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'minerals',
            'workforce',
            'permits',
        ]);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_start_mining(string $location, string $mineralType)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Mineral = Mineral::where('mineral_ore', $mineralType)->firstOrFail();
        $this->setMinerLevel($Mineral->miner_level);

        $response = $this->post('/mine/start', [
            'mineral_ore' => $mineralType,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [
            'avail_workforce',
            'new_permits',
            'new_hunger',
        ]]);

        $this->assertDatabaseHas('miner', [
            'user_id' => $this->RandomUser->id,
            'mineral_ore' => $Mineral?->mineral_ore,
            'location' => $location,
        ]);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_user_cannot_start_mining_in_wrong_location(string $location, string $mineralOre)
    {
        if ($location === GameLocations::GOLBAK_LOCATION->value) {
            $this->setUserCurrentLocation(GameLocations::SNERPIIR_LOCATION->value, $this->RandomUser);
        } else {
            $this->setUserCurrentLocation(GameLocations::GOLBAK_LOCATION->value, $this->RandomUser);
        }

        $response = $this->post('/mine/start', [
            'mineral_ore' => $mineralOre,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_user_cannot_start_mining_with_too_few_permits(string $location, string $mineralOre)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();
        $Miner->permits = 0;
        $Miner->save();

        $response = $this->post('/mine/start', [
            'mineral_ore' => $mineralOre,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_user_cannot_start_mining_if_no_workforce(string $location, string $mineralOre)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Mineral = Mineral::where('mineral_ore', $mineralOre)->firstOrFail();

        $this->setMinerLevel($Mineral->miner_level);

        $this->MinerWorkforce->avail_workforce = 0;
        $this->MinerWorkforce->save();

        $response = $this->post('/mine/start', [
            'mineral_ore' => $mineralOre,
            'workforce_amount' => 1,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_user_cannot_start_mining_if_already_mining(string $location, string $mineralOre)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Mineral = Mineral::where('mineral_ore', $mineralOre)->firstOrFail();
        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();

        $this->setMinerLevel($Mineral->miner_level);

        $Miner->mineral_ore = $mineralOre;
        $Miner->mining_finishes_at = Carbon::now()->addMinutes(4);
        $Miner->save();

        $response = $this->post('/mine/start', [
            'mineral_ore' => $mineralOre,
            'workforce_amount' => 3,
        ]);

        $response->assertStatus(422);
        $response->json();

    }

    /**
     * @dataProvider locationProvider
     */
    public function test_user_cannot_mine_mineral_if_miner_is_too_level(string $location, string $mineralOre)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        $Mineral = Mineral::where('mineral_ore', $mineralOre)->firstOrFail();

        if ($Mineral->miner_level === 1) {
            $this->assertTrue(true);
        } else {
            $this->setMinerLevel(1);

            $response = $this->post('/mine/start', [
                'mineral_ore' => $mineralOre,
                'workforce_amount' => 1,
            ]);

            $response->assertStatus(422);
            $response->json();
        }
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_end_mining(string $location, string $mineralOre)
    {
        $this->setUserCurrentLocation($location, $this->RandomUser);

        Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->update([
            'mineral_ore' => $mineralOre,
            'mining_finishes_at' => Carbon::now()->subMinutes(1),
        ]);

        $response = $this->post('/mine/end', [
            'is_cancelling' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [
            'avail_workforce',
            'new_hunger',
        ]]);

        $this->assertDatabaseHas('miner', [
            'user_id' => $this->RandomUser->id,
            'mineral_ore' => null,
            'location' => $location,
        ]);
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_cant_end_mining_with_no_active_mining(string $location, string $mineralOre)
    {
        $Mineral = Mineral::where('mineral_ore', $mineralOre)->firstOrFail();
        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setMinerLevel(44);

        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();

        $Miner->mineral_ore = null;
        $Miner->save();

        $MinerWorkforce = MinerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();

        $MinerWorkforce->avail_workforce -= 3;
        $MinerWorkforce->{$location} = 3;
        $MinerWorkforce->save();

        $response = $this->post('/mine/end', [
            'mineral_type' => $mineralOre,
            'workforce_amount' => 1,
            'is_cancelling' => false,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_cant_end_mining_with_not_finished_mining(string $location, string $mineralOre)
    {
        $Mineral = Mineral::where('mineral_ore', $mineralOre)->first();

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setMinerLevel($Mineral->miner_level);

        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();

        $Miner->mineral_ore = $mineralOre;
        $Miner->mining_finishes_at = Carbon::now()->addMinutes(15);
        $Miner->save();

        Carbon::setTestNow(now()->addMinutes(10));

        $MinerWorkforce = MinerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        $MinerWorkforce->avail_workforce -= 3;
        $MinerWorkforce->{$location} = 3;
        $MinerWorkforce->save();

        $response = $this->post('/mine/end', [
            'mineral_type' => $mineralOre,
            'workforce_amount' => 1,
            'is_cancelling' => false,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_cant_cancel_finished_mining(string $location, string $mineralOre)
    {
        $Mineral = Mineral::where('mineral_ore', $mineralOre)->first();

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setMinerLevel($Mineral->miner_level);

        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();

        $Miner->mineral_ore = $mineralOre;
        $Miner->mining_finishes_at = Carbon::now()->addMinutes(5);
        $Miner->save();

        Carbon::setTestNow(Carbon::now()->addMinutes(10));

        $MinerWorkforce = MinerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        $MinerWorkforce->avail_workforce -= 3;
        $MinerWorkforce->{$location} = 3;
        $MinerWorkforce->save();

        $response = $this->post('/mine/end', [
            'mineral_type' => 'tomato',
            'workforce_amount' => 1,
            'is_cancelling' => true,
        ]);

        $response->assertStatus(422);
        $response->json();
    }

    /**
     * @dataProvider locationProvider
     */
    public function test_cancel_mining_doesnt_give_xp_or_ores(string $location, string $mineralOre)
    {
        $Mineral = Mineral::where('mineral_ore', $mineralOre)->first();

        $this->setUserCurrentLocation($location, $this->RandomUser);
        $this->setMinerLevel($Mineral->miner_level);

        $Miner = Miner::where('user_id', $this->RandomUser->id)->where('location', $location)->firstOrFail();

        $Miner->mineral_ore = $mineralOre;
        $Miner->mining_finishes_at = Carbon::now()->addMinutes(5);
        $Miner->save();

        $MinerWorkforce = MinerWorkforce::where('user_id', $this->RandomUser->id)->firstOrFail();
        $MinerWorkforce->avail_workforce -= 3;
        $MinerWorkforce->{$location} = 3;
        $MinerWorkforce->save();

        $response = $this->post('/mine/end', [
            'is_cancelling' => true,
        ]);

        $response->assertStatus(200);
        $response->json();

        $this->assertDatabaseMissing('inventory', [
            'username' => $this->RandomUser->username,
            'item' => $Mineral->mineral_ore,
        ]);

        $this->assertResponseNotHasEvent($response, GameEvents::XpGainedEvent->value);
        $UserLevels = UserLevels::where('user_id', $this->RandomUser->id)->first();
        if (! $UserLevels instanceof UserLevels) {
            $this->fail();
        }

        $this->assertEquals($this->RandomUser->userLevels->miner_xp, $UserLevels->miner_xp);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function locationProvider()
    {
        return [
            'iron' => [GameLocations::GOLBAK_LOCATION->value, 'iron ore'],
            'adron' => [GameLocations::SNERPIIR_LOCATION->value, 'adron ore'],
        ];
    }
}
