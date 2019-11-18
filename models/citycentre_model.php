<?php
    class CityCentre_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            
            $sql = "SELECT artefact FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['artefact_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT permits FROM miner WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined in statement 1
            $stmt->execute();
            $data['permits'] = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $data;
        }
    }
?>