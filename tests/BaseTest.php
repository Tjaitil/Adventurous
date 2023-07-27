<?php

namespace App\tests;

use App\tests\support\MockApp;
use App\tests\support\RequestTrait;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    use RequestTrait, MockApp;

    public static function setUpBeforeClass(): void
    {
        self::setEnv();
    }
}
