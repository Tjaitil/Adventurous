<?php
    class citycentre_model extends model  {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function getData() {
            $data = array();
            
            $sql = "SELECT artefact, uses_left FROM keep WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['artefact_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT permits FROM miner WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined in statement 1
            $stmt->execute();
            $data['permits'] = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $data;
        }
        public function changeArtefact($artefact) { //AJAX function
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $artefact;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0 || $row['amount'] == 0) {
                $this->gameMessage("ERROR: You don't have that artefact in your inventory", true);
                return false;
            }
            
            $sql = "SELECT artefact, uses FROM keep WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            //Lagre uses et sted, men hvor? Inventory med en ekstra kolonne med special? 
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE keep SET artefact=:artefact WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                if($row2['uses'] > 0) {
                    update_inventory($this->db->conn, $this->username, "damaged " . $row2['artefact'] . '(' . $row2['uses'] . ')', 1);
                }
                
                update_inventory($this->db->conn, $this->username, $artefact, -1, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
            js_echo(array($artefact, $amount));
        }
        public function buyPermits($permit_amount) {
            //AJAX function
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