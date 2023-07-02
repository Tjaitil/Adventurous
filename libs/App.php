<?php

namespace App\libs;

use App\libs\database;
use Dotenv\Dotenv;

class App
{

    private static $instance = null;

    /**
     * Get database instance
     *
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    public function boot()
    {
        require_once(ROUTE_ROOT . constant('ROUTE_HELPER') . 'general_helpers.php');

        Dotenv::createImmutable(__DIR__ . '/../')->load();
        database::getInstance()->openConn();
    }
}
