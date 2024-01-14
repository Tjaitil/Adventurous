<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\UserTrait;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, UserTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->__constructUserTrait();
    }
}
