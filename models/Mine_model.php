<?php
    class Mine_model extends model  {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData($js = false) {
            $data = array();
            
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $sql = "SELECT permits, mining_type, mining_countdown, fetch_minerals FROM miner
                    WHERE location=:location AND username=:username";          
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data['minerData'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $param_minerlevel = $this->session['miner']['level'];
            $param_location = $this->session['location'];
            if($js === true) {
                $sql2 = "SELECT mineral_type, miner_level, experience, time, permit_cost, location 
                FROM minerals_data ORDER BY miner_level ASC";
            }
            else {
                $sql2 = "SELECT mineral_type, permit_cost FROM minerals_data ORDER BY miner_level ASC";
            }
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->execute();
            $data['mineral_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT avail_workforce, efficiency_level FROM miner_workforce WHERE username=:username";         
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['workforce_data'] = $stmt3->fetch(PDO::FETCH_ASSOC);
            if($js === true) {
                $this->response->addTo("data", $data['mineral_types'], array("index" => "mineral_types"));
                $this->response->addTo("data", $data['workforce_data'], array("index" => "workforce_data"));
            }
            else {
                return $data;
            }
        }
        
        // Function to echo date for ajax request
        public function checkCountdown() {
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $sql = "SELECT mining_countdown, fetch_minerals, mining_type FROM miner WHERE location=:location AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date_timestamp_get(new DateTime($row['mining_countdown']));
            $this->db->closeConn();
            $this->response->addTo("data", $date, array("index" => "data"));
            $this->response->addTo("data", $row['fetch_minerals'], array("index" => "fetch_minerals"));
            $this->response->addTo("data", $row['mining_type'], array("index" => "mining_type"));
        }
        public function cancelMining() {
            if(in_array($this->session['location'], array('golbak', 'snerpiir')) != true) {
                    return false;   
            }
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $workforce = $this->session['location'] . '_workforce';
            $sql = "SELECT m.mining_countdown, m.fetch_minerals, mw.avail_workforce, mw.$workforce FROM miner as m
                    INNER JOIN miner_workforce as mw ON mw.username = m.username
                    WHERE m.location=:location AND m.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date = date("Y-m-d H:i:s");
            $date_timestamp = date_timestamp_get(new DateTime($date));
            $countdown_timestamp = date_timestamp_get(new DateTime($row['mining_countdown']));
            if($date_timestamp > $countdown_timestamp  && $row['fetch_minerals'] == 0) {
                $this->response->addTo("errorGameMessage", "You currently have no mining going on");
                return false;
            }
            else if($date_timestamp > $countdown_timestamp && $row['fetch_minerals'] == 1) {
                $this->response->addTo("errorGameMessage", "Why would you cancel completed mining");
                return false;
            }   
            $time = strtotime($date);
            $time = $time - (15 * 60);
            $date = date("Y-m-d H:i:s", $time);
            try {
                $this->db->conn->beginTransaction();
                $param_mining_countdown = $date;
                $param_avail_workforce = $row['avail_workforce'] + $row[$workforce];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $sql = "UPDATE miner as m INNER JOIN miner_workforce as mw ON mw.username = m.username
                        SET m.mining_type='none', m.fetch_minerals=0, m.mining_countdown=:mining_countdown,
                        mw.avail_workforce=:avail_workforce, mw.$workforce=0
                        WHERE location=:location AND m.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mining_countdown", $param_mining_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                    
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->response->addTo("gameMessage", "You have canceled your mining");
            $this->response->addTo("data", $param_avail_workforce, array("index" => "availWorkforce"));            
        }
    }
?>