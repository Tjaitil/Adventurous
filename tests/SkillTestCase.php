<?php

namespace Tests;

use Tests\Utils\Traits\ExperienceAssertions;

abstract class SkillTestCase extends TestCase
{
    use ExperienceAssertions;

    public function setUp(): void
    {
        parent::setUp();
    }
}
