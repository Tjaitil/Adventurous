<?php

namespace Tests\Feature\Events;

use App\Enums\SkillNames;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CheckLevelTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();

        $this->beginDatabaseTransaction();

        $this->actingAs($this->TestUser);
    }

    public function test_check_level_route()
    {
        $response = $this->get('/skill/level-check');

        $response->json();
        $response->assertStatus(200);
    }

    public function test_check_level_route_updates_skills()
    {
        $CurrentLevel = $this->TestUserLevels->miner_level;

        $this->setSkillLevelUpAble(SkillNames::MINER->value);
        $response = $this->get('/skill/level-check');
        $response->assertStatus(200);

        $response->json();

        $response->assertJsonStructure([
            'new_levels',
        ]);

        $this->assertDatabaseHas('user_levels', [
            'user_id' => $this->TestUser->id,
            'miner_level' => $CurrentLevel + 1,
        ]);
    }
}
