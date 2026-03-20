<?php

namespace Tests\Feature\Guides;

use Tests\TestCase;

class GuideRetrievalTest extends TestCase
{
    public function test_api_can_get_guide()
    {
        $response = $this->actingAs($this->getRandomUser())
            ->getJson('/guides/skills/farmer');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'title',
                'level_requirement',
                'slug',
                'html',
            ])
            ->assertJsonPath('title', 'Farmer skill');
    }

    public function test_api_returns_404_for_nonexistent_guide()
    {
        $response = $this->actingAs($this->getRandomUser())
            ->getJson('/guides/skills/nonexistent');

        $response->assertStatus(404)
            ->assertJsonPath('error', 'Guide not found');
    }

    public function test_api_can_list_guides_payload()
    {
        $response = $this->actingAs($this->getRandomUser())
            ->getJson('/guides');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'categories',
                'categoryGuides',
            ]);
    }
}
