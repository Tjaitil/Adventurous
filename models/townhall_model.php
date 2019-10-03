<?php
    class townhall_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $location = $this->session['location'];
            $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
            if (array_search($location, $locations) === false) {
                header("/maintenance");
                exit();
            }
            
            $sql = "SELECT {$location} FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $row;
        }
    }
?>