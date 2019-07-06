<?php
    class Armory_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData($js = false) {
            $sql = "SELECT warrior_id, helm, left_hand, body, right_hand, legs, boots FROM warrior_armory WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->closeConn();
            if($js === true) {
                get_template('armory', array('warrior_armory' => $row), true);
            }
            else {
                return $row;
            }
        }
        
        public function wearArmor($warrior_id, $item) {
            $armory = array("Iron", "Steel", "Gargonite", "Adron", "yeqdon", "Frajrite");
            $result = false;
            foreach($armory as $key) {
                if(strpos($item, $key) !== false) {
                    $result = true;
                }
            }
            if(!$result === true) {
                $this->gameMessage("ERROR: The item you are trying to put doesn't exists", true);
                return false;
            }
            
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to put armor on doesn't exist", true);
                return false;
            }
            
            $sql = "SELECT type FROM armory WHERE name=:name";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $param_name = $item;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(empty($row['type'])) {
                $this->gameMessage("ERROR: Type of armor doesn't exists", true);
                return false;
            }
            
            $sql = "SELECT " . $row['type'] . " FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE warrior_armory SET " . $row['type'] . "=:item WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                
                if($row2[$row['type']] != 'none') {
                    update_inventory($this->conn, $this->username, $row2[$row['type']], 1);
                }
                update_inventory($this->conn, $this->username, $item, -1);
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $data = get_inventory($this->conn, $this->username);
            $this->closeConn();
        }
        
        public function removeArmor($warrior_id, $item, $part) {
            $parts = array("helm", "left_hand", "body", "right_hand", "legs", "boots");
            if(array_search($part, $parts) === false) {
                $this->gameMessage("That part does not exists", true);
                return false;
            }
            
            $sql = "SELECT " . $part . " FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
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
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to put armor on doesn't exist", true);
                return false;
            }
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE warrior_armory SET " . $part . "='none' WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                update_inventory($this->conn, $this->username, $item, 1);
            
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $data = get_inventory($this->conn, $this->username);
            $this->closeConn();
        }
        
    }
?>