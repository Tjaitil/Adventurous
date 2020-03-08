<?php
    class SetMine_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function setMine($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set mining
            $mineral = $POST['mineral'];
            $workforce = $POST['workforce'];
            
            if($this->session['hunger'] < 10) {
                $this->gameMessage("ERROR: Your hunger is too high, please eat!", true);
                return false;
            }
            $sql = "SELECT m.mining_type, m.permits, mw.avail_workforce, mw.efficiency_level
                    FROM miner AS m INNER JOIN miner_workforce AS mw ON mw.username = m.username
                    WHERE location=:location AND m.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['mining_type'] != 'none') {
                $this->gameMessage("ERROR: There is already mining going on!", true);
                return false;
            }
            if($row['avail_workforce'] < $workforce) {
                $this->gameMessage("ERROR: You don't have that many workforce available", true);
                return false;
            }
            $sql = "SELECT miner_level, experience, time, permit_cost FROM minerals_data
                    WHERE mineral_type=:mineral_type AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mineral_type", $param_mineral_type, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_mineral_type = $mineral;
            $param_location = $this->session['location'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['miner_level'] > $this->session['miner']['level']) {
                $this->gameMessage("ERROR: You are too low level to mine this mineral", true);
                return false;
            }
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to mine this mineral", true);
                return false;
            }
            if($row2['permit_cost'] > $row['permits']) {
                $this->gameMessage("ERROR: You don't have enough permits!", true);
                return false;
            }
            
            $addTime = $row2['time'] - (10 * $row['efficiency_level']);
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            
            $experience = ($row2['experience'] * 0.20);
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE miner SET mining_type=:mining_type, mining_countdown=:mining_countdown,
                        permits=:permits, fetch_minerals=1 WHERE location=:location AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mining_type", $param_mining_type, PDO::PARAM_STR);
                $stmt->bindParam(":mining_countdown", $param_mining_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR); 
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mining_type = $mineral;
                $param_mining_countdown = date_format($newDate, "Y-m-d H:i:s");
                $param_permits = $row['permits'] - $row2['permit_cost'];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE miner_workforce SET avail_workforce=:avail_workforce,
                {$this->session['location']}_workforce=:miner_workforce WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":miner_workforce", $param_miner_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_avail_workforce = $row['avail_workforce'] - $workforce;
                $param_miner_workforce = $workforce;
                $param_username = $this->username;
                $stmt2->execute();
                
                // Update xp
                $this->UpdateGamedata->updateXP('miner', $experience);
                
                $this->db->conn->commit();
                }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            jsecho(array($param_permits, $param_avail_workforce));
        }
    }
?>