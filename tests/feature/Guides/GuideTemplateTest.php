<?php

namespace Tests\Feature\Guides;

use Tests\TestCase;

class GuideTemplateTest extends TestCase
{
    public function test_guide_service_can_retrieve_farmer_guide()
    {
        $guideService = app(\App\Services\GuideService::class);
        $guide = $guideService->getGuide('skills', 'farmer');

        $this->assertNotNull($guide);
        $this->assertEquals('Farmer skill', $guide['title']);
        $this->assertEquals(1, $guide['level_requirement']);
        $this->assertEquals('farmer', $guide['slug']);
    }

    public function test_farmer_guide_processes_crops_table_template()
    {
        $guideService = app(\App\Services\GuideService::class);
        $guide = $guideService->getGuide('skills', 'farmer');

        // Verify the guide HTML contains a table (template was processed)
        $this->assertStringContainsString('<table>', $guide['html']);
        $this->assertStringContainsString('Level', $guide['html']);
        $this->assertStringContainsString('Crop', $guide['html']);
    }

    public function test_farmer_guide_processes_workforce_upgrades_template()
    {
        $guideService = app(\App\Services\GuideService::class);
        $guide = $guideService->getGuide('skills', 'farmer');

        // Verify workforce upgrades table is in the HTML
        $this->assertStringContainsString('Efficiency', $guide['html']);
        $this->assertStringContainsString('Required Farmer Level', $guide['html']);
        $this->assertStringContainsString('Upgrade Cost', $guide['html']);
    }

    public function test_farmer_guide_includes_toc()
    {
        $guideService = app(\App\Services\GuideService::class);
        $guide = $guideService->getGuide('skills', 'farmer');

        $this->assertStringContainsString('guide-toc', $guide['html']);
    }

    public function test_guide_service_can_list_skills()
    {
        $guideService = app(\App\Services\GuideService::class);
        $guides = $guideService->listByCategory('skills');

        $this->assertIsArray($guides);
        $this->assertGreaterThan(0, count($guides));
    }
}
