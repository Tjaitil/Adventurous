<?php
    class Armory_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData() {
            $sql = "SELECT warrior_id, helm, ammunition, ammunition_amount, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                    (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                    FROM warrior_armory
                    WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $sql = "SELECT type FROM warriors WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            for($i = 0; $i < count($row); $i++) {
                $row[$i]['type'] = $row2[$i]['type'];  
            }
            $this->db->closeConn();
            
            return $row;
        }
        public function wearArmor($POST) {
            // $POST variable holds the post data
            $this->warrior_id = $POST['warrior_id'];
            $item = strtolower($POST['item']);
            // If $hand variable is not equals to false it means that a specific hand is selected for the sword, dagger or spear
            $hand = $POST['hand'];
            $amount = ($POST['amount'] === 'false') ? 1 : $POST['amount'];
            
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite", "oak", "beech", "yew");
            $items = array("sword", "dagger", "shield", "platebody", "platelegs", "helm", "arrows", "bow", "knives");
            // $melee_items containing sword, dagger, shield
            $type_items['melee'] = array_slice($items, 0, 6);
            // $ranged_items containing arrows, bow, knives
            $type_items['ranged'] = array_slice($items, 2);
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
            
            $item_data = get_item($this->session['inventory'], $item);
            
            if(!isset($item_data['item'])) {
                $this->gameMessage("ERROR: You don't have that item in your inventory", true);
                return false;
            }
            else if($amount !== false && $item_data['amount'] < $amount) {
                $this->gameMessage("ERROR: You don't have that amount in your inventory", true);
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
            
            $sql = "SELECT warrior_id, type FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to put armor on doesn't exist", true);
                return false;
            }
            
            $warrior_type = $stmt->fetch(PDO::FETCH_OBJ)->type;
            if(in_array($item_array[1], $type_items[$warrior_type]) === false) {
                $this->gameMessage("ERROR: " . $warrior_type . " cannot wear " . $item, true);
                return false;
            }
            
            $sql = "SELECT type FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($row['type'])) {
                $this->gameMessage("ERROR: Type of armor doesn't exists", true);
                return false;
            }
            
            // Check if the warrior is wearing either knives or bow
            $ranged_test = in_array($item_array[1], array("bow"));
            $melee_test = in_array($item_array[1], array("dagger", "sword"));
        
            if($row['type'] == 'hand' || $row['type'] == 'left_hand') {
                if($ranged_test !== false) {
                    $row['type'] = 'right_hand';
                    $other_hand = 'left_hand';
                }
                else {
                    if($row['type'] == 'hand') {
                        $row['type'] = $hand  .'_hand';    
                    }
                    $other_hand = ($row['type'] == 'right_hand') ? 'left_hand' : 'right_hand';
                }

                $sql = "SELECT {$row['type']}, {$other_hand} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";    
            }
            else if ($row['type'] == 'ammunition') {
                $sql = "SELECT {$row['type']}, ammunition_amount FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";   
            }
            else {
                $sql = "SELECT {$row['type']} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username"; 
            }
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $ranged_test = in_array($item_array[1], array("knives", "bow"));
            if(isset($row2['right_hand']) && $row2['right_hand'] !== 'none') {
                $ranged_test2 = in_array(explode(" ", $row2['right_hand'])[1], array("knives", "bow"));    
            }
            else {
                $ranged_test2 = false;
            }
            
            try {
                $this->db->conn->beginTransaction();
                if($row['type'] === 'ammunition') {
                    $sql = "UPDATE warrior_armory SET {$row['type']}=:item, ammunition_amount=:ammunition_amount
                            WHERE warrior_id=:warrior_id AND username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $stmt->bindParam(":ammunition_amount", $param_ammunition_amount, PDO::PARAM_INT);
                    $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_item = $item;
                    if($row2[$row['type']] !== $item) {
                        $param_ammunition_amount = $amount;
                    }
                    else {
                        $param_ammunition_amount = $amount + $row2['ammunition_amount'];
                    }
                    $param_warrior_id = $this->warrior_id;
                    $param_username = $this->username;
                    $stmt->execute();
                }
                else {
                    if($ranged_test !== false) {
                        $sql = "UPDATE warrior_armory SET right_hand=:item, left_hand='none'
                                WHERE warrior_id=:warrior_id AND username=:username";
                    }
                    else if($ranged_test2 !== false && $row['type'] == 'left_hand') {
                        $sql = "UPDATE warrior_armory SET right_hand='none', left_hand=:item
                                WHERE warrior_id=:warrior_id AND username=:username";    
                    }
                    else {
                        $sql = "UPDATE warrior_armory SET {$row['type']}=:item WHERE warrior_id=:warrior_id AND username=:username";   
                    }
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_item = $item;
                    $param_warrior_id = $this->warrior_id;
                    $param_username = $this->username;
                    $stmt->execute();   
                }
                if($row2[$row['type']] != 'none' && $row['type'] != 'ammunition') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row2[$row['type']], 1);
                }
                else if($row2[$row['type']] != 'none' && $row['type'] == 'ammunition' && $row2[$row['type']] !== $item) {
                    // Update inventory if there is other type of ammunition equipped
                    $this->UpdateGamedata->updateInventory($row2[$row['type']], $row2['ammunition_amount']);
                }
                if($ranged_test2 !== false && $row['type'] === 'left_hand') {
                    $this->UpdateGamedata->updateInventory($row2['right_hand'], 1);
                }
                // If ranged test is not false then remove the piece
                if(isset($row2['left_hand']) && $ranged_test !== false) {
                    if($row2['left_hand'] != 'none') {
                        $this->UpdateGamedata->updateInventory($row2['left_hand'], 1);
                    }
                }
                if($row['type'] === 'ammunition') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -$amount, true);        
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -1, true);  
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->getWarriorstats();
            $this->db->closeConn();
        } 
        public function removeArmor($POST) {
            // $POST variable holds the post data
            $this->warrior_id = $POST['warrior_id'];
            $part = strtolower($POST['part']);
            
            $parts = array("helm", "ammunition", "left_hand", "body", "right_hand", "legs", "boots");
            if(array_search($part, $parts) === false) {
                $this->gameMessage("ERROR: That part does not exists", true);
                return false;
            }
            if($part === 'ammunition') {
                $sql = "SELECT {$part}, ammunition_amount FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";   
            }
            else {
                $sql = "SELECT {$part} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            }
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The soldier you are trying to remove armor from doesn't exist", true);
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                
                if($part === 'ammunition') {
                    $sql = "UPDATE warrior_armory SET {$part}='none', ammunition_amount=0
                            WHERE warrior_id=:warrior_id AND username=:username";
                }
                else {
                    $sql = "UPDATE warrior_armory SET {$part}='none' WHERE warrior_id=:warrior_id AND username=:username";
                }
                
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_id = $this->warrior_id;
                $param_username = $this->username;
                $stmt->execute();    
                
                if($part === 'ammunition') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row[$part], $row['ammunition_amount'], true);    
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row[$part], 1, true);
                }
            
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->getWarriorstats();
            $this->db->closeConn();
        }
        public function getWarriorstats() {
            // Perform a query with two subqueries which retrieves the sum of attack and the sum of defence as well as armor
            $sql = "SELECT warrior_id, type, helm, ammunition, ammunition_amount, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                    (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
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