<?php

namespace Tests\Feature\Events;

use App\Enums\SkillNames;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateSkillsTest extends TestCase
{
    use DatabaseTransactions;

    protected $connectionsToTransact = ['mysql'];

    public function setUp(): void
    {
        parent::setUp();

        $this->beginDatabaseTransaction();

        $this->actingAs($this->TestUser);
    }

    public function test_route()
    {
        $response = $this->post('/skills/update');

        $response->json();
        $response->assertJsonStructure([
            'user_levels',
            'new_levels',
        ]);
        $response->assertStatus(200);
    }

    public function test_route_updates_skills()
    {
        $CurrentLevel = $this->TestUserLevels->miner_level;

        $this->setSkillLevelUpAble(SkillNames::MINER->value);
        $response = $this->post('/skills/update');
        $response->assertStatus(200);

        $response->json();

        $response->assertJsonStructure([
            'new_levels',
            'user_levels',
        ]);

        $this->assertDatabaseHas('user_levels', [
            'user_id' => $this->TestUser->id,
            'miner_level' => $CurrentLevel + 1,
        ]);
    }
}
