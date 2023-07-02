<?php

namespace App\libs;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Capsule\Manager as DB;

use \PDO;
use \PDOException;

final class database
{
    private static $instance = null;

    public PDO $conn;

    private $DB_SERVER;
    private $DB_NAME;
    private $DB_USERNAME;
    private $DB_PASS;

    private $testing = false;

    public static function getSelf()
    {
        return new static();
    }

    /** */
    private function __construct()
    {
        $this->DB_SERVER = $_SERVER['DB_SERVER'];
        $this->DB_USERNAME  = $_SERVER["DB_USERNAME"];
        $this->DB_NAME = $_SERVER["DB_NAME"];
        $this->DB_PASS = $_SERVER["DB_PASS"];
        $this->openConn();
    }

    /**
     * Create new database connection
     *
     * @return void
     */
    public function openConn()
    {
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $_SERVER["DB_SERVER"],
            'database' => $_SERVER["DB_NAME"],
            'username' => $_SERVER["DB_USERNAME"],
            'password' => $_SERVER["DB_PASS"],
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
        ]);


        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();


        if ($this->testing) {
            DB::beginTransaction();
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->DB_SERVER . ";dbname=" . $this->DB_NAME,
                $this->DB_USERNAME,
                $this->DB_PASS
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOexception $e) {
            mail('miner123@hotmail.no', 'Database ERROR', $e->getMessage(), 'FROM: <system@adventurous.no');
            die("ERROR: Could not connect" . $e->getMessage());
        }
    }

    public function rollBack()
    {
        DB::rollBack();
    }

    public function closeConn()
    {
        unset($this->conn);
    }

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
}
