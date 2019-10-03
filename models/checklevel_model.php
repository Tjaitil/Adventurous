<?php
    class checklevel_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function getData($level) {
            $sql = "SELECT next_level FROM level_data WHERE level=:level";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
            $param_level = $level;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $row['next_level'];
        }
    }
?>