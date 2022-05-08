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
            $param_username = $this->username;
            $sql = "SELECT DISTINCT 
                    a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.left_hand, 
                    a.body, a.right_hand, a.legs, a.boots, b.type, 
                        (SELECT SUM(attack) 
                         FROM armory_items_data 
                         WHERE item 
                         IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.boots)) 
                         AS attack, 
                         (SELECT SUM(defence) 
                         FROM armory_items_data 
                         WHERE item 
                         IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.boots)) 
                         AS defence 
                    FROM warrior_armory as a 
                    LEFT JOIN warriors as b 
                    ON a.warrior_id = b.warrior_id and a.username = b.username 
                    WHERE a.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            
            return $row;
        }
        public function wearArmor($POST) {
            $this->warrior_id = $POST['warrior_id'];
            $item = strtolower($POST['item']);
            $amount = ($POST['amount'] === 'false') ? 1 : $POST['amount'];
            
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite", "oak", "beech", "yew");
            $items = array("sword", "dagger", "shield", "platebody", "platelegs", "helm", "arrows", "bow", "throwing");
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
                $this->response->addTo("errorGameMessage", "The item you are trying to put doesn't exists");
                return false;
            }
            
            $item_data = get_item($this->session['inventory'], $item);
            
            if(!isset($item_data['item'])) {
                $this->response->addTo("errorGameMessage", "You don't have that item in your inventory");
                return false;
            }
            else if($amount !== false && $item_data['amount'] < $amount) {
                $this->response->addTo("errorGameMessage", "You don't have that amount in your inventory");
                return false;
            }

            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $sql = "SELECT DISTINCT
                    a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.body, a.right_hand, 
                    a.left_hand, a.legs, a.boots,
                    b.stamina_level, b.technique_level, b.strength_level, b.precision_level,
                    c.type
                    FROM warrior_armory as a 
                    INNER JOIN warriors_levels as b ON a.warrior_id = b.warrior_id AND a.username = b.username
                    INNER JOIN warriors as c ON a.warrior_id = c.warrior_id AND a.username = c.username
                    WHERE a.warrior_id=:warrior_id AND a.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "The soldier you are trying to put armor on doesn't exist");
                return false;
            }
            $warrior_type = $row['type'];
            if(in_array($item_array[1], $type_items[$warrior_type]) === false) {
                $this->response->addTo("errorGameMessage", $warrior_type . " cannot wear " . $item . "because of type");
                return false;
            }
            // Commented out for debuggin purposes

            // $total_level = intval($row['stamina_level']) + intval(['technique_level']) +
            //                intval($row['strength_level']) + intval($row['precision_level']);
            // $level_required = array("iron" => 4, "steel" => 9, "gargonite" => 15, "adron" => 25, "yeqdon" =>32,
            //                         "frajrite" => 40, "wujkin" => 42);
            // if($level_required[$item_array[0]] > $total_level) {
            //     $this->response->addTo("errorGameMessage", "The warriors level is not high enough to wear equipment of this mineral");
            //     return false;
            // }
            
            // If mineral is frajrite or wujkin check if it has been unlocked
            if(in_array($item_array[0], array('frajrite', 'wujkin'))) {
                $table_name = $item_array[0] . '_items';
                $sql = "SELECT {$table_name} FROM user_data WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage", "You don't have permission to wear this armour");
                    return false;
                } else if($this->session['profiency'] !== 'warrior') {
                    $this->response->addTo("errorGameMessage", "To wear {$item_array[0]} armor you need to have warrior profiency");
                    return false;
                }
            }
            
            $param_item = $item;
            $sql = "SELECT type FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(empty($row2['type'])) {
                $this->response->addTo("errorGameMessage", "Type of armor doesn't exist");
                return false;
            }
            $type = $row2['type'];
            $bow_item = in_array($item_array[1], array("bow"));
            if($bow_item === true) {
                $type = 'right_hand';
            }
            // Check wether or not right and left hand is filled
            $right_hand_filled = false;
            $left_hand_filled = false;
            $other_ammunition = false;
            try {
                $this->db->conn->beginTransaction();
                if($type === 'ammunition') {
                    if($item_array[1] === 'throwing') {
                        $case = 1;
                        if($row['right_hand'] !== 'none') {
                            $case = 2;
                            $right_hand_filled = true;
                        }
                        if($row['left_hand'] !== 'none' && strpos($row['left_hand'], 'shield')) {
                            $case = 3;
                            $left_hand_filled = true;
                        }
                        switch ($case) {
                            case 1:
                                $sql = "UPDATE warrior_armory SET ammunition=:item, ammunition_amount=:ammunition_amount
                                WHERE warrior_id=:warrior_id AND username=:username";
                                break;
                            case 'value':
                                $sql = "UPDATE warrior_armory SET right_hand='none', ammunition=:item, 
                                        ammunition_amount=:ammunition_amount
                                WHERE warrior_id=:warrior_id AND username=:username";
                                break;
                            case 'value':
                                $sql = "UPDATE warrior_armory SET right_hand='none', left_hand='none',
                                    ammunition=:item, ammunition_amount=:ammunition_amount
                                WHERE warrior_id=:warrior_id AND username=:username";
                                break;
                        }
                    }
                    else {
                        $sql = "UPDATE warrior_armory SET {$type}=:item, ammunition_amount=:ammunition_amount
                            WHERE warrior_id=:warrior_id AND username=:username";
                    }
                    // Check for other ammunition equipped
                    if($row[$type] !== $item && $row[$type] !== 'none') {
                        $other_ammunition = true;
                    }
                    if($other_ammunition === true) {
                        $param_ammunition_amount = $amount;
                    }
                    else {
                        $param_ammunition_amount = $amount + $row['ammunition_amount'];
                    }
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $stmt->bindParam(":ammunition_amount", $param_ammunition_amount, PDO::PARAM_INT);
                    $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_item = $item;
                    $param_warrior_id = $this->warrior_id;
                    $param_username = $this->username;
                    $stmt->execute();
                }
                else {
                    if($bow_item !== false) {
                        $sql = "UPDATE warrior_armory SET right_hand=:item, left_hand='none'
                                WHERE warrior_id=:warrior_id AND username=:username";
                    }
                    else if($bow_item !== false && $type == 'left_hand') {
                        $sql = "UPDATE warrior_armory SET right_hand='none', left_hand=:item
                                WHERE warrior_id=:warrior_id AND username=:username";    
                    }
                    else {
                        // Set hand
                        if($type === 'hand' && $bow_item === false) {
                            $type = $POST['hand'] . '_hand';
                        }
                        $sql = "UPDATE warrior_armory SET {$type}=:item WHERE warrior_id=:warrior_id AND username=:username";   
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
                if($item_array[1] === 'throwing') {
                    if($right_hand_filled === true) {
                        $this->UpdateGamedata->updateInventory($row['right_hand'], 1);
                    }
                    if($left_hand_filled === true ) {
                        $this->UpdateGamedata->updateInventory($row['left_hand'], 1);
                    }
                }
                if($other_ammunition === true) {
                    $this->UpdateGamedata->updateInventory($row['ammunition'], $row['ammunition_amount']);
                }
                // Remove left hand weapon when equiping bow
                if($bow_item === true && $row['left_hand'] !== 'none') {
                    $this->UpdateGamedata->updateInventory($row[$type], 1);
                }
                else if($row[$type] !== 'none' && $type !== 'ammunition') {
                    // If there is already something equipped update inventory
                    $this->UpdateGamedata->updateInventory($row[$type], 1);
                }
                if($type === 'ammunition') {
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
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
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
                $this->response->addTo("errorGameMessage", "That part does not exists");
                return false;
            }
            if($part === 'ammunition') {
                $sql = "SELECT {$part}, ammunition_amount FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";   
            }
            else {
                $sql = "SELECT {$part} FROM warrior_armory WHERE warrior_id=:warrior_id AND username=:username";
            }
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
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
                $this->response->addTo("errorGameMessage", "The soldier you are trying to remove armor from doesn't exist");
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
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->getWarriorstats();
            $this->db->closeConn();
        }
        public function getWarriorstats() {
            // Perform a query with two subqueries which retrieves the sum of attack and the sum of defence as well as armor
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            
            $sql = "SELECT DISTINCT 
            a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.left_hand, 
            a.body, a.right_hand, a.legs, a.boots, b.type, 
                (SELECT SUM(attack) 
                 FROM armory_items_data 
                 WHERE item 
                 IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.boots)) 
                 AS attack, 
                 (SELECT SUM(defence) 
                 FROM armory_items_data 
                 WHERE item 
                 IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.boots)) 
                 AS defence 
            FROM warrior_armory as a 
            LEFT JOIN warriors as b 
            ON a.warrior_id = b.warrior_id and a.username = b.username 
            WHERE a.username=:username AND a.warrior_id=:warrior_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $this->warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ob_start();
            get_template('armory', $row2, true);
            $this->response->addTo("html", ob_get_clean());
        }
    }
