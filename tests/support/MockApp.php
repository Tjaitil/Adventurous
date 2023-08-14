<?php

namespace App\tests\support;

use App\libs\App;
use App\libs\database;
use App\models\UserData;
use Dotenv\Dotenv;

trait MockApp
{
    public static $isConfigured = false;
    public static $username = "";
    public static $user_id = 0;



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

        self::$isConfigured = true;
        self::$username = $_ENV['TEST_USER'];
        self::$user_id = UserData::where('username', self::$username)->first()->id ?? 0;
    }
}
