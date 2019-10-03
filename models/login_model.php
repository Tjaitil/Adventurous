<?php
    class Login_model extends model {
        public $row;
        public $status = false;
        public $profiency;
        
        function __construct() {
            parent::__construct();
        }
        
        public function checkUser($username, $password) {
            $sql = "SELECT username, password FROM users WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            $this->row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0) {
                $this->status = true;
                
            }
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->profiency = $row['profiency'];
            $this->db->closeConn();
        }
    }
?>