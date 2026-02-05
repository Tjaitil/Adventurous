<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\InventoryTrait;
use Tests\Support\UserLevelsTrait;
use Tests\Support\UserTrait;
use Tests\Utils\Traits\EventAssertions;
use TiMacDonald\Log\LogFake;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, EventAssertions, InventoryTrait, UserLevelsTrait, UserTrait, RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->__constructUserTrait();
        $this->__constructUserLevelsTrait();
        // LogFake::bind();
    }
}
