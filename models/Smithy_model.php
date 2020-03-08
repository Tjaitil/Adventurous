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
            $ore = $mineral . ' ' . 'ore';
            $minerals = array("iron", "steel", "gargonite", "adron", "yeqdon", "frajrite");
            if(in_array($mineral, $minerals) == false) {
                $this->gameMessage("ERROR: You are not allowed to smith from that mineral", true);
                return false;
            }
            
            $sql = "SELECT item, amount FROM inventory WHERE (item=:item OR item='gold') AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $ore;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 1) {
                $this->gameMessage("ERROR: You missing one or more items in your inventory", true);
                return false;
            }
            
            $sql = "SELECT mineral_required, level, cost FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: That item does not exist!", true);
                return false;
            }
            if($row2['level'] > $this->session['miner']['level']) {
                $this->gameMessage("ERROR: Your level is too low", true);
                return false;
            }
            
            if(strpos($item, 'arrows') !== false) {
                $item_data = get_item($this->session, 'unfinished arrow');
                if(!$item_data['amount'] < 5 || $item_data['amount'] < $amount) {
                    $this->gameMessage("ERROR: You don't have enough unfinished arrows in your inventory", true);
                    return false;    
                }
            }
            
            $minerals_needed = $row2['mineral_required'] * $amount;
            if($row2['mineral_required'] * $amount > $row[0]['amount']) {
                $this->gameMessage("ERROR! You dont have enough ores", true);
                return false;
            }
            $cost = $row2['cost'] * $amount;
            if($row[1]['amount'] < $cost) {
                $this->gameMessage("ERROR! You don't have enough gold", true);
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
                $this->UpdateGamedata->updateInventory($ore , -$minerals_needed, true);
                
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