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
        $this->determineEnvironment();
        $this->isRollbackMode = $_SERVER["ROLLBACK_MODE"] == "true";
        database::getInstance()->openConn();
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



    /**
     *
     * @return void
     */
    public function determineEnvironment()
    {
        if (php_sapi_name() == "cli") {
            $this->isMocking = true;
        } else {
            $this->isMocking = false;
        }
    }
}
