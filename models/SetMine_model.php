<?php
    class SetMine_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false, true);
        }
        public function setMine($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set mining
            $mineral = strtolower($POST['mineral']);
            $workforce = $POST['workforce'];
            if($this->hungerModel->checkHunger()) {
                $this->response->addTo("errorGameMessage", $this->hungerModel->getHungerError());
                return false;
            } 

            $param_location = $this->session['location'];
            $param_username = $this->username;
            $sql = "SELECT m.mining_type, m.permits, mw.avail_workforce, mw.efficiency_level
                    FROM miner AS m INNER JOIN miner_workforce AS mw ON mw.username = m.username
                    WHERE location=:location AND m.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['mining_type'] != 'none') {
                $this->response->addTo("errorGameMessage" , "There is already mining going on!");
                return false;
            }
            if($row['avail_workforce'] < $workforce) {
                $this->response->addTo("errorGameMessage" , "You don't have that many workforce available");
                return false;
            }

            $param_mineral_type = $mineral;
            $param_location = $this->session['location'];
            $sql = "SELECT miner_level, experience, time, permit_cost FROM minerals_data
                    WHERE mineral_type=:mineral_type AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mineral_type", $param_mineral_type, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['miner_level'] > $this->session['miner']['level']) {
                $this->response->addTo("errorGameMessage" , "You are too low level to mine this mineral");

                return false;
            }
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage" , "You are in the wrong city to mine this mineral");

                return false;
            }
            if($row2['permit_cost'] > $row['permits']) {
                $this->response->addTo("errorGameMessage" , "You don't have enough permits!");
                return false;
            }
            
            $addTime = $row2['time'] - (0.1 * $row['efficiency_level'] + $workforce * 0.05);
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            
            $experience = ($row2['experience'] * 0.20);
            try {
                $this->db->conn->beginTransaction();

                $this->hungerModel->setHunger('skill');
                
                $param_mining_type = strtolower($mineral);
                $param_mining_countdown = date_format($newDate, "Y-m-d H:i:s");
                $param_permits = $row['permits'] - $row2['permit_cost'];
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $sql = "UPDATE miner SET mining_type=:mining_type, mining_countdown=:mining_countdown,
                        permits=:permits, fetch_minerals=1 WHERE location=:location AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mining_type", $param_mining_type, PDO::PARAM_STR);
                $stmt->bindParam(":mining_countdown", $param_mining_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR); 
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_avail_workforce = $row['avail_workforce'] - $workforce;
                $param_miner_workforce = $workforce;
                $param_username = $this->username;
                $sql2 = "UPDATE miner_workforce 
                         SET avail_workforce=:avail_workforce, {$this->session['location']}_workforce=:miner_workforce 
                         WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":miner_workforce", $param_miner_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->execute();
                
                // Only gain xp when miner level is below 30 or if profiency is miner
                if($this->session['miner']['level'] < 30 || $this->session['profiency'] == 'miner') { 
                    $this->response->addTo("levelUP", $this->UpdateGamedata->updateXP('miner', $experience));
                }
                
                $this->db->conn->commit();
                }
            catch (Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("data" , $experience, array("index" => "xpGained"));
            $this->response->addTo("data" , $param_avail_workforce, array("index" => "availWorkforce"));
            $this->response->addTo("data" , $param_permits, array("index" => "permits"));
            $this->response->addTo("data" , $this->hungerModel->getHunger(), array("index" => "newHunger"));
            $this->response->addTo("gameMessage" , "Mining started, {$experience} miner xp gained");
        }
    }
?>