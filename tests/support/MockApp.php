<?php

namespace App\tests\support;

use App\libs\database;
use App\libs\session;
use Dotenv\Dotenv;

trait MockApp
{
    public static $isConfigured = false;
    public static $username = "";

    public static function setEnv()
    {
        if (self::$isConfigured) {
            return;
        }


        require('root/routes.php');
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        self::$username = $_ENV['TEST_USER'];

        self::$isConfigured = true;
        database::getInstance()->openConn();
    }

    protected function tearDown(): void
    {
        database::getInstance()->rollBack();
    }
}
