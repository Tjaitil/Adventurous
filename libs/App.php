<?php

namespace App\libs;

use App\libs\database;
use Dotenv\Dotenv;

class App
{
    private static $instance = null;
    private $isMocking = false;
    private $isRollbackMode = false;



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



    /**
     * 
     * @return void 
     */
    public function boot()
    {
        require_once(ROUTE_ROOT . constant('ROUTE_HELPER') . 'general_helpers.php');

        Dotenv::createImmutable(__DIR__ . '/../')->load();
        database::getInstance()->openConn();

        $this->isMocking = $isMocking = $_SERVER["MOCKING"] == "true";
        $this->isRollbackMode = $isRollbackMode = $_SERVER["ROLLBACK_MODE"] == "true";
    }



    /**
     * 
     * @return bool
     */
    public function getIsMocking()
    {
        return $this->isMocking;
    }



    /**
     * 
     * @return bool
     */
    public function getIsRollbackMode()
    {
        return $this->isRollbackMode;
    }
}
