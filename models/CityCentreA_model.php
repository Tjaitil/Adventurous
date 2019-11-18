<?php
    class CityCentreA_model extends model {
        // Class is a grouping of functions which can be called from ajax request from /citycentre
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->UpdateGamedata = $this->loadModel('UpdateGamedata');
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
                
                update_inventory($this->db->conn, $this->username, 'gold', -50, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("You bought 50 permits for the price of 100 gold", true);
        }
        public function upgradeLodge($skill) {
            if(!in_array($skill, array('farmer', 'miner', 'warrior'))) {
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
                
                update_inventory($this->db->conn, $this->username, 'gold', -$level_data['cost']);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
            }
            if($skill == 'warrior') {
                $this->gameMessage("You have upgraded your armycamp to level {$new_level}", true);
            }
            else {
                $this->gameMessage("You have upgraded your workforce lodge to level {$new_level}", true);
            }
        }
    }
?>