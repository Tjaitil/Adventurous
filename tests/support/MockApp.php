<?php

namespace App\tests\support;

use App\libs\App;
use Dotenv\Dotenv;

trait MockApp
{
    use SessionTrait;
    public static $isConfigured = false;

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
        App::getInstance()->boot();
        self::initializeUser($_ENV['TEST_USERNAME']);
        self::$isConfigured = true;
    }
}
