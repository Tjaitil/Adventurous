<?php
    class Highscores_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }

        public function getData() {
            $data = array();
            
            $sql = "SELECT username, farmer_level, farmer_xp FROM user_levels ORDER BY farmer_xp DESC LIMIT 50";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data['farmer_highscores'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            $sql2 = "SELECT username, miner_level, miner_xp FROM user_levels ORDER BY miner_xp DESC LIMIT 50";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->execute();
            $data['miner_highscores'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT username, warrior_level, warrior_xp FROM user_levels ORDER BY warrior_xp DESC LIMIT 50";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->execute();
            $data['warrior_highscores'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
            $sql4 = "SELECT username, trader_level, trader_xp FROM user_levels ORDER BY trader_xp DESC LIMIT 50";
            $stmt4 = $this->db->conn->prepare($sql4);
            $stmt4->execute();
            $data['trader_highscores'] = $stmt4->fetchAll(PDO::FETCH_ASSOC);
            
            $sql5 = "SELECT username, (farmer_xp + miner_xp + trader_xp + warrior_xp) as 'total_xp',
                            (farmer_level + miner_level + trader_level + warrior_level) as 'total_level'
                     FROM user_levels ORDER BY total_xp DESC"; 
            $stmt5 = $this->db->conn->prepare($sql5);
            $stmt5->execute();
            $data['total_highscores'] = $stmt5->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $data;
        }
        
        /*public function getHighscores($type) {
            switch($type) {
                case 'farmer':
                    $sql = "SELECT farmer_level, xp FROM user_levels ORDER BY xp LIMIT 50";
                    break;
                case 'miner':
                    $sql = "SELECT miner_level,  miner_xp FROM user_levels ORDER BY miner_xp LIMIT 50";
                    break;
                case 'trader':
                    $sql = "SELECT trader_level, trader_xp FROM user_levels ORDER BY trader_xp LIMIT 50";
                    break;
                case 'warrior':
                    $sql = "SELECT warrior_level, warrior_xp FROM user_levels ORDER BY warrior_xp LIMIT 50";
                    break;
            }
            
        }*/
    }
?>