<?php
    class mine_model extends model  {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData() {
            $data = array();
            
            $sql = "SELECT miner_level, permits, mining_type, mining_countdown, fetch_minerals FROM miner
                    WHERE username=:username";          
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['minerData'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT mineral_type, permit_cost FROM minerals_data WHERE miner_level <=:minerlevel AND location=:location";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":minerlevel", $param_minerlevel, PDO::PARAM_STR);
            $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_minerlevel = $this->session['miner']['level'];
            $param_location = $this->session['location'];
            $stmt2->execute();
            $data['mineral_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT avail_workforce, effect_level FROM miner_workforce WHERE username=:username";         
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['workforceData'] = $stmt3->fetch(PDO::FETCH_ASSOC);

            $this->closeConn();
            return $data;
        }
        
        // Function to echo date for ajax request
        public function checkCountdown($check = false) {
            $sql = "SELECT mining_countdown, fetch_minerals FROM miner WHERE username= :username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $miningCountdown = $row['mining_countdown'];
            $date = date_timestamp_get(new DateTime($miningCountdown));
            
            if($check == true) {
                $now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
                if($now > $date) {
                    return true;
                }
                else if($now < $date) {
                    return false;
                }
            }
            $this->closeConn();
            js_echo(array($date, $row['fetch_minerals']));
        }
    }
?>