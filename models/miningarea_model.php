<?php
    class miningarea_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        /*public function getDat() {¨
            $sql = "SELECT ";  
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_ASSOC);
        }*/
    }
?>