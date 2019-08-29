<?php
    class model {
        public $conn;

        function __construct () {
                if(defined('DB_SERVER') || defined('DB_username') || defined('DB_pass') || defined('DB_name')) {
                    try {
                        $this->conn = new PDO("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
                        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }
                    catch (PDOexception $e) {
                        die ("ERROR: Could not connect.i" . $e->getMessage());
                    } 
                }
                else {
                    define('DB_server', 'localhost');
                    define('DB_username', 'root');
                    define('DB_pass', '');
                    define('DB_name', 'Adventurous');
                    try {
                        $this->conn = new PDO("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
                        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }
                    catch (PDOexception $e) {
                        die ("ERROR: Could not connect." . $e->getMessage());
                    } 
                }
        }
        
        public function gameMessage($message, $ajax = false) {
            $date = '[' . date("H:i:s") . '] ';
            if($ajax != false) {
                echo $date . $message;
            }
            else {
                $_SESSION['gamedata']['game_message'] = $date . $message;
            }
        }
        public function closeConn() {
            unset($this->conn);
        }
    }
?>