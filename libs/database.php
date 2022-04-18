<?php
    final class database {
        public $conn;
        private $DB_SERVER; 
        private $DB_NAME; 
        private $DB_USERNAME;  
        private $DB_PASS;
        
        function __construct () {
            $this->DB_SERVER = $_SERVER['DB_SERVER'];
            $this->DB_NAME = $_SERVER["DB_NAME"];
            $this->DB_USERNAME  = $_SERVER["DB_USERNAME"];
            $this->DB_PASS = $_SERVER["DB_PASS"];
            $this->openConn();
        }
        public function openConn() {
            if($this->conn != null) {
                return;
            }
            else {
                try {
                    $this->conn = new PDO("mysql:host=" . $this->DB_SERVER . ";dbname=" . $this->DB_NAME, $this->DB_USERNAME, 
                    $this->DB_PASS);
                    $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
                catch (PDOexception $e) {
                    mail('miner123@hotmail.no', 'Database ERROR', $e->getMessage(), 'FROM: <system@adventurous.no');
                    die ("ERROR: Could not connect" . $e->getMessage());
                }
            }
        }
        public function closeConn() {
            unset($this->conn);
        }
    }
?>