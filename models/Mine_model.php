<?php
    class Mine_model extends model  {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            
            $sql = "SELECT permits, mining_type, mining_countdown, fetch_minerals FROM miner
                    WHERE location=:location AND username=:username";          
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $data['minerData'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT mineral_type, permit_cost FROM minerals_data ORDER BY miner_level ASC";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":minerlevel", $param_minerlevel, PDO::PARAM_STR);
            $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_minerlevel = $this->session['miner']['level'];
            $param_location = $this->session['location'];
            $stmt2->execute();
            $data['mineral_types'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT avail_workforce, efficiency_level FROM miner_workforce WHERE username=:username";         
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['workforceData'] = $stmt3->fetch(PDO::FETCH_ASSOC);

            return $data;
        }
        
        // Function to echo date for ajax request
        public function checkCountdown() {
            $sql = "SELECT mining_countdown, fetch_minerals, mining_type FROM miner WHERE location=:location AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $date = date_timestamp_get(new DateTime($row['mining_countdown']));
            $this->db->closeConn();
            js_echo(array($date, $row['fetch_minerals'], $row['mining_type']));
        }
        public function cancelMining() {
            if(in_array($this->session['location'], array('golbak', 'snerpiir')) != true) {
                    return false;   
            }
            $workforce = $this->session['location'] . '_workforce';
            $sql = "SELECT m.mining_countdown, m.fetch_minerals, mw.avail_workforce, mw.$workforce FROM miner as m
                    INNER JOIN miner_workforce as mw ON mw.username = m.username
                    WHERE m.location=:location AND m.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date = date("Y-m-d H:i:s");
            $date_timestamp = date_timestamp_get(new DateTime($date));
            $countdown_timestamp = date_timestamp_get(new DateTime($row['mining_countdown']));
            if($date_timestamp > $countdown_timestamp  && $row['fetch_minerals'] == 0) {
                $this->gameMessage("ERROR: You currently have no mining going on", true);
                return false;
            }
            else if($date_timestamp > $countdown_timestamp && $row['fetch_minerals'] == 1) {
                $this->gameMessage("ERROR: Why would you cancel completed mining?", true);
                return false;
            }   
            $time = strtotime($date);
            $time = $time - (15 * 60);
            $date = date("Y-m-d H:i:s", $time);
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE miner as m INNER JOIN miner_workforce as mw ON mw.username = m.username
                        SET m.mining_type='none', m.fetch_minerals=0, m.mining_countdown=:mining_countdown,
                        mw.avail_workforce=:avail_workforce, mw.$workforce=0
                        WHERE location=:location AND m.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mining_countdown", $param_mining_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mining_countdown = $date;
                $param_avail_workforce = $row['avail_workforce'] + $row[$workforce];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                    
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            // $echo_data holds game data to update data in the client in form of JSON for easier accessibility
            $echo_data = array();
            $this->gameMessage("You have canceled your mining", true);
            echo "|";
            $echo_data['avail_workforce'] = $param_avail_workforce;
            echo json_encode($echo_data);
        }
    }
?>