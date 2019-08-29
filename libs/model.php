<?php
    class model {
        public $conn;

        function __construct () {
            try {
                $this->conn = new PDO("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch (PDOexception $e) {
                die ("ERROR: Could not connect.i" . $e->getMessage());
            } 
                /*else {
                    define('DB_server', 'localhost');
                    define('DB_username', 'root');
                    define('DB_pass', '');
                    define('DB_name', 'Adventurous');
                    try {
                        $this->conn = new PDO("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
                        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    }
                    catch (PDOexception $e) {
                        die ("ERROR: Could not connectas." . $e->getMessage());
                    } 
                }*/
        }
        
        public function gameMessage($message, $ajax = false) {
            $date = '[' . date("H:i:s") . '] ';
            if($ajax != false) {
                echo $date . $message;
            }
            $_SESSION['gamedata']['log'][] = $date . $message;
            if(count($_SESSION['gamedata']['log']) > 15) {
                unset($_SESSION['gamedata']['log'][0]);
                $_SESSION['gamedata']['log'] = array_values($_SESSION['gamedata']['log']);
            }
         }
        public function closeConn() {
            unset($this->conn);
        }
    }
?>