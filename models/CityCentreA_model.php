<?php
    class CityCentreA_model extends model {
        // Class is a grouping of functions which can be called from ajax request from /citycentre
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function buyPermits($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from citycentre.js
            // Function to buy mine permits for user
            $permit_amount = $POST['amount'];
            if($this->session['gold'] < 50) {
                $this->gameMessage("ERROR: You don't have enough gold!", true);
                return false;
            }
            
            $sql = "SELECT permits FROM miner WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE miner SET permits=:permits WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_permits = $row2['permits'] + $permit_amount;
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -50, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("You bought 50 permits for the price of 50 gold", true);
        }
        public function upgradeLodge($skill) {
            if(!in_array($skill, array('farmer', 'miner', 'warrior'))) {
                $this->errorHandler->reportError(array($this->username, "Not valid skill: " . $skill . __METHOD__));
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            $sql = "SELECT level FROM lodge WHERE profiency=:profiency AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":profiency", $param_skill, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_skill = $skill;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $new_level = $row['level'] + 1;
            
            switch($skill) {
                case 'farmer':
                    
                    break;
                case 'miner':
                    
                    break;
                case 'warrior':
                    $level_data = $armycamp[$new_level];
                    break;
            }
            
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE lodge SET level=:level WHERE profiency=:profiency AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
                $stmt->bindParam(":profiency", $param_skill, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_level = $new_level;
                $param_skill = $skill;
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -$level_data['cost'], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            if($skill == 'warrior') {
                $this->gameMessage("You have upgraded your armycamp to level {$new_level}", true);
            }
            else {
                $this->gameMessage("You have upgraded your workforce lodge to level {$new_level}", true);
            }
        }
        public function unlockArmorItems($POST) {
            $type = trim($POST['type']);
            $table_name = str_replace(" ", "_", trim($POST['type']));
            
            $sql = "SELECT {$table_name} FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Check if items is already unlocked
            if($row[$table_name] == 1) {
                $this->gameMessage("ERROR: You have already unlocked {$type}", true);
                return false;
            }
            
            $sql = "SELECT amount FROM inventory WHERE item='weapon tokens' AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $amount = $stmt->fetch(PDO::FETCH_OBJ)->amount;
        
            $cost = array("frajite items" =>  125, "wujkin items" => 160);      
            
            // Check if player has enough weapon tokens to unlock
            if($amount < $cost[$type]) {
                $this->gameMessage("ERROR: You don't have enough weapons tokens", true);
                return false;
            }
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE user_data SET {$table_name}=1 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('weapon tokens', - $cost[$type], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->gameMessage("You have unlocked {$type}", true);
        }
    }
?>