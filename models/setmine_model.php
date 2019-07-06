<?php
    class setmine_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getMineralTypeData($mineral_type) {
            $sql = "SELECT experience, time, permit_cost FROM minerals_data WHERE mineral_type=:mineral_type AND location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":mineral_type", $param_mineral_type, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_mineral_type = $mineral_type;
            $param_location = $this->session['location'];
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to grow this crop!");
                return false;
            }
            $data = array();
            $data['mineral'] = $stmt->fetch(PDO::FETCH_ASSOC);
            unset($stmt);
            return $data;
        }
        
        public function setMineData($mining_data) {
            $experience = $mining_data['experience'] + $this->session['miner']['xp'];
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE miner SET mining_type=:mining_type, mining_countdown=:mining_countdown,
                        permits=:permits, fetch_minerals=1 WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":mining_type", $param_mining_type, PDO::PARAM_STR);
                $stmt->bindParam(":mining_countdown", $param_mining_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mining_type = $mining_data['mineral_type'];
                $param_mining_countdown = $mining_data['mining_countdown'];
                $param_permits = $mining_data['permits'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE miner_workforce SET avail_workforce=:avail_workforce,
                         miner_workforce=:miner_workforce WHERE username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":miner_workforce", $param_miner_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_avail_workforce = $mining_data['new_workforce'];
                $param_miner_workforce = $mining_data['workforce_quant'];
                $param_username = $this->username;
                $stmt2->execute();
                
                update_xp($this->conn, $this->username, 'miner', $experience);
                
                $this->conn->commit();
                }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            $this->closeConn();
            $_SESSION['gamedata']['miner']['miner_xp'] = $experience;
        }
    }
?>