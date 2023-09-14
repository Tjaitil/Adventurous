<?php

namespace App\tests\support;

use App\models\UserData;

trait SessionTrait
{
    public static $username = "";
    public static $user_id = 0;

    /**
     * @param string $username
     * @return void 
     */
    public static function initializeUser(string $username)
    {
        self::$username = $username;
        self::$user_id = UserData::where('username', self::$username)->first()->id ?? 0;
    }
}
