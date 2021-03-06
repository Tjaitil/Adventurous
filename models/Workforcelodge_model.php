<?php
    class workforcelodge_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $profiences = array('farmer', 'miner');
            $data = array();
            
            $sql = "SELECT workforce_total, towhar_workforce, krasnur_workforce, avail_workforce, efficiency_level
                    FROM farmer_workforce WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['farmer_workers'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT workforce_total, golbak_workforce, snerpiir_workforce, avail_workforce, efficiency_level
                    FROM miner_workforce WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['miner_workers'] = $stmt->fetch(PDO::FETCH_ASSOC);
        
            $array = array(1,2);
            $in  = str_repeat('?,', count($array) - 1) . '?';
            $sql3 = "SELECT level, max_farm_workers, max_mine_workers FROM level_data WHERE level IN ($in)";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->execute(array($this->session['farmer']['level'], $this->session['miner']['level']));
            $data['workforce_cap'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
        
            $this->db->closeConn();
            return $data;
        }
    }
?>