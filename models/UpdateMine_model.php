<?php
    class UpdateMine_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, true);
        }
        public function updateMine() {
            // Function to reset the mine, free up workforce and give player the minerals they have received
            // This function is called from an AJAX request from mine.js
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $sql = "SELECT mining_type FROM miner WHERE location=:location AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $mining_type = $row['mining_type'];
            
            $param_mining_type = $mining_type;
            $sql2 = "SELECT experience, min_per_period, max_per_period FROM minerals_data WHERE mineral_type=:mineral_type";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":mineral_type", $param_mining_type, PDO::PARAM_STR);
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT avail_workforce, {$this->session['location']}_workforce, mineral_quant_level
                     FROM miner_workforce WHERE username=:username";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            $rand_min = $row2['min_per_period'];
            $rand_max = $row2['max_per_period'];
            $quantity = rand($rand_min, $rand_max);
            
            $experience = $row2['experience'];
            try {
                $this->db->conn->beginTransaction();
                
                $param_username = $this->username;
                $sql = "UPDATE miner SET mining_type='none',
                        fetch_minerals=0 WHERE username =:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_avail_workforce = $row3['avail_workforce'] + $row3[$this->session['location']. '_workforce'];
                $param_miner_workforce = 0;
                $sql2 = "UPDATE miner_workforce SET avail_workforce=:avail_workforce, {$this->session['location']}_workforce=:miner_workforce
                         WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt2->bindParam(":miner_workforce", $param_miner_workforce, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt2->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($mining_type . ' ore', $quantity, true);
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
            $experience = $row2['experience'];
            $this->response->addTo("gameMessage", "You received {$quantity} of ".ucfirst($mining_type.' ore') .",
                                   gaining a total of {$experience} mining xp");
            $this->response->addTo("data", $param_avail_workforce, array("index" => "availWorkforce"));
        }
    }
?>