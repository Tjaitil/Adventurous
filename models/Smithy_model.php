<?php
    class Smithy_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData() {
            $data = array();
            $sql = "SELECT item, mineral_required, cost FROM armory_items_data ORDER BY mineral_required";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $minerals = array("iron", "steel", "clay", "gargonite", "adron", "yeqdon", "frajrite");
            foreach($minerals as $key) {
                $mineral = $key;
                $data[$key] = array_filter($row, function($key) use ($mineral) {
                    return stripos($key['item'], $mineral) !== false;
                });
            }
            return $data;
        }
        public function smith($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from smithy.js
            // Function to smith items from minerals
            $item = strtolower($POST['item']);
            $mineral = strtolower($POST['mineral']);
            $amount = $POST['amount'];
            $required = (strpos($item, 'bar')) ? $mineral . ' ore' : $mineral . ' bar';
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite");
            if(in_array($mineral, $minerals) == false) {
                $this->response->addTo("errorGameMessage" , "You are not allowed to smith from that mineral");
                return false;
            }
            // Check if player has the required ore and gold in inventory
            $param_item = $required;
            $param_username = $this->username;
            $sql = "SELECT item, amount FROM inventory WHERE (item=:item OR item='gold') AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($stmt->rowCount() == 1) {
                $this->response->addTo("errorGameMessage" , "You missing one or more items in your inventory");
                return false;
            }
            if($row[0]['item'] === 'gold') {
                $row['gold'] = $row[0];
                $row['item'] = $row[1];
            }
            else {
                $row['gold'] = $row[1];
                $row['item'] = $row[0];
            }
            
            $sql = "SELECT mineral_required, level, cost FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage" , "That item does not exist!");
                return false;
            }
            if($row2['level'] > $this->session['miner']['level']) {
                $this->response->addTo("errorGameMessage" , "Your level is too low");
                return false;
            }
            if(strpos($item, 'arrows') !== false) {
                $item_data = get_item($this->session, 'unfinished arrow');
                if(!$item_data['amount'] < 5 * $amount) {
                    $this->response->addTo("errorGameMessage" , "You don't have enough unfinished arrows in your inventory");
                    return false;    
                }
            }
            
            $minerals_needed = $row2['mineral_required'] * $amount;
            if($row2['mineral_required'] * $amount > $row['item']['amount']) {
                $this->response->addTo("errorGameMessage" , "You dont have enough ores");
                return false;
            }
            $cost = $row2['cost'] * $amount;
            // If profiency is farmer pay 20 % less
            if($this->session['profiency'] === 'miner') {
                $cost*= 0.80;
            }
            if($row['gold']['amount'] < $cost) {
                $this->response->addTo("errorGameMessage" , "You don't have enough gold");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                if(strpos($item, 'arrows') !== false) {
                    $this->UpdateGamedata->updateInventory($item, $amount * 5);
                    $this->UpdateGamedata->updateInventory('unfinished arrow', -$minerals_needed * 5, true);
                }
                else {
                    // Update inventory if item is not type of arrows
                    $this->UpdateGamedata->updateInventory($item, $amount);
                }
                
                if($this->session['profiency'] !== 'miner') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory('gold', -$cost);   
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($required, -$minerals_needed, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>