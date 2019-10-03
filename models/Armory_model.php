<?php
    class Armory_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function getData() {
            $sql = "SELECT warrior_id, helm, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                    (SELECT SUM(defence) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                    FROM warrior_armory
                    WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            
            return $row;
        }
        public function wearArmor($warrior_id, $item, $hand = false) {
            $this->warrior_id = $warrior_id;
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite");
            $items = array("sword", "spear", "dagger", "shield", "platebody", "platelegs", "helm");
            // Check out if the $item matches $mineral and $item
            $item_array = explode(" ", $item);
            if(array_search($item_array[0], $minerals) === false) {
                $result = true;
            }
            if(array_search($item_array[1], $items) === false) {
                $result = true;
            }
            if(isset($result)) {
                $this->gameMessage("ERROR: The item you are trying to put doesn't exists", true);
                return false;
            }
            
            if(in_array($item_array[0], array('frajrite', 'wujkin'))) { 
                $sql = "SELECT {$item_array[0]} FROM warrior_permissions WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_NUM);
                if($row[0] == 0) {
                    $this->gameMessage("ERROR: You don't have permission to wear this armour", true);
                    return false;
                }
            }
            
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to put armor on doesn't exist", true);
                return false;
            }
            
            $sql = "SELECT type FROM smithy_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(empty($row['type'])) {
                $this->gameMessage("ERROR: Type of armor doesn't exists", true);
                return false;
            }
            if($row['type'] == 'hand') {
                $row['type'] = $hand  .'_hand';
            }
            $sql = "SELECT {$row['type']} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE warrior_armory SET {$row['type']}=:item WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                
                if($row2[$row['type']] != 'none') {
                    update_inventory($this->db->conn, $this->username, $row2[$row['type']], 1);
                }
                update_inventory($this->db->conn, $this->username, $item, -1, true);
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->getWarriorstats();
            $this->db->closeConn();
        } 
        public function removeArmor($warrior_id, $item, $part) {
            $this->warrior_id = $warrior_id;
            $parts = array("helm", "left_hand", "body", "right_hand", "legs", "boots");
            if(array_search($part, $parts) === false) {
                $this->gameMessage("ERROR: That part does not exists", true);
                return false;
            }
            
            $sql = "SELECT {$part} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row[$part] != $item) {
                $this->gameMessage("ERROR: The selected warrior is not wearing that", true);
                return false;
            }
            
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to remove armor from doesn't exist", true);
                return false;
            }
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warrior_armory SET {$part}='none' WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                update_inventory($this->db->conn, $this->username, $item, 1, true);
            
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->getWarriorstats();
            $this->db->closeConn();
        }
        public function getWarriorstats() {
            $sql = "SELECT warrior_id, helm, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                    (SELECT SUM(defence) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                    FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $row[0]['check'] = true;
            get_template('armory', $row, true);
        }
    }
?>