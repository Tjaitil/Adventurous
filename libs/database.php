<?php
    final class database {
        public $conn;
        
        function __construct () {
            $this->openConn();
        }
        public function openConn() {
            if($this->conn != null) {
                return;
            }
            else {
                try {
                    $this->conn = new PDO("mysql:host=" . DB_server . ";dbname=" . DB_name, DB_username, DB_pass);
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