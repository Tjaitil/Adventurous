<?php
    class UpdateMine_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function updateMine() {
            $sql = "SELECT mining_type FROM miner WHERE location=:location AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $mining_type = $row['mining_type'];
            
            $sql2 = "SELECT experience, min_per_period, max_per_period FROM minerals_data WHERE mineral_type=:mineral_type";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":mineral_type", $param_mining_type, PDO::PARAM_STR);
            $param_mining_type = $mining_type;
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            // Endre experience;
            /*$mineralEffectivity = 1 *(1 + 0.25 * $this->data['workforceData']['effect_level']);*/
            
            $sql3 = "SELECT avail_workforce, {$this->session['location']}_workforce, mineral_quant_level, effect_level
                     FROM miner_workforce WHERE username=:username";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            
            $rand_min = $row2['min_per_period'] * (1.25 * $row3['effect_level']) + (0.5 * $row3[$this->session['location'] . '_workforce']);
            $rand_max = $row2['max_per_period'] * (1.25 * $row3['effect_level']) + (0.5 * $row3[$this->session['location'] . '_workforce']);
            $quantity = rand($rand_min, $rand_max);
            $experience = ($row2['experience'] * 0.80) + $this->session['miner']['xp'];
            
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE miner SET mining_type=:mining_type,
                        fetch_minerals=0 WHERE username =:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":mining_type", $param_mining_type, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mining_type = 'none';
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE miner_workforce SET avail_workforce=:avail_workforce, {$this->session['location']}_workforce=:miner_workforce
                         WHERE username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt2->bindParam(":miner_workforce", $param_miner_workforce, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_avail_workforce = $row3['avail_workforce'] + $row3[$this->session['location']. '_workforce'];
                $param_miner_workforce = 0;
                $param_username = $this->username;
                $stmt2->execute();
                
                update_inventory($this->conn, $this->username, $mining_type . ' ore', $quantity, true);
                update_xp($this->conn, $this->username, 'miner', $row2['experience'] + $this->session['miner']['xp']);
                
                $this->conn->commit();
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $_SESSION['gamedata']['miner']['xp'] = $experience;
            $this->gameMessage("You received {$quantity} of " . ucfirst($mining_type . 'ore'), true);
            $this->closeConn();
        }
    }
?>