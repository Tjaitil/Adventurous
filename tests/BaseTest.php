<?php

namespace App\tests;

use App\libs\database;
use App\tests\support\DatabaseTest;
use App\tests\support\MockApp;
use App\tests\support\RequestTrait;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    use RequestTrait, MockApp, DatabaseTest;

    public static function setUpBeforeClass(): void
    {
        self::setEnv();
    }

    protected function setUp(): void
    {
        $this->startTransaction();
    }

    protected function tearDown(): void
    {
        $this->rollbackTransaction();
    }
}
