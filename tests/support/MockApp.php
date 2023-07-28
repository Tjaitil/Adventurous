<?php

namespace App\tests\support;

use App\libs\database;
use Dotenv\Dotenv;

trait MockApp
{
    public static $isConfigured = false;
    public static $username = "";



    /**
     * 
     * @return void 
     * @throws \Dotenv\Exception\InvalidPathException 
     * @throws \Dotenv\Exception\InvalidEncodingException 
     * @throws \Dotenv\Exception\InvalidFileException 
     * @throws \Throwable 
     */
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
}
