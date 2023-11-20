<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\UserTrait;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, UserTrait;
}
