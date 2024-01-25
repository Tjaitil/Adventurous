<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\InventoryTrait;
use Tests\Support\UserTrait;
use TiMacDonald\Log\LogFake;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, UserTrait, InventoryTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->__constructUserTrait();

        LogFake::bind();
    }
}
