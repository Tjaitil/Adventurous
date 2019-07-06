<?php
    class workforcelodge_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData($profiency) {
            $profiences = array('farmer', 'miner');
            
            
            $sql = "SELECT workforce_total, avail_workforce, crop_workforce, butch_workforce FROM " .
                    $profiency ."_workforce WHERE username=:username";
            var_dump($sql);
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            
            $sql2 = "SELECT level FROM workforce_lodge WHERE username=:username and profiency=:profiency";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt2->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
            $param_profiency = $profiency;
            $stmt2->execute();
            $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            $param_level = $row['level'];
            
            $sql3 = "SELECT max_workers FROM workforce_lodge_data WHERE level=:level";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(":level", $param_level, PDO::PARAM_STR);
            $stmt->execute();
            $stmt3->execute();
            $data = array();
            $data['workforce_level'] = $param_level;
            $data['workers'] = $stmt->fetch(PDO::FETCH_ASSOC);
            $data['workforce_building']  = $stmt3->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $data;
        }
    }
?>