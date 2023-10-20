<?php
    class ArmorItems extends model {
        // Class is a grouping of functions which can be called from ajax request from /citycentre
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function unlockArmorItems($POST) {
            $type = trim($POST['type']);
            $table_name = str_replace(" ", "_", trim($POST['type']));
            
            $param_username = $this->username;
            $sql = "SELECT {$table_name} FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            // Check if items is already unlocked
            if($row[$table_name] == 1) {
                $this->response->addTo("errorGameMessage", "You have already unlocked {$type}");
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
                $this->response->addTo("errorGameMessage", "You don't have enough weapons tokens");
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
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->response->addTo("gameMessage", "You have unlocked {$type}");
        }
    }
?>