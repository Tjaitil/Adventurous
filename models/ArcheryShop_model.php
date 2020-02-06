<?php
    class ArcheryShop_model extends model {
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
            $items = implode('|', array("oak", "beech", "yew", "unfinished arrow", "arrow shaft"));
            $sql = "SELECT item, wood_required, cost FROM armory_items_data WHERE item REGEXP '{$items}'";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }
        public function fletch($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from smithy.js
            // Function to smith items from minerals
            $item = strtolower($POST['item']);
            $amount = $POST['amount'];
            var_dump($item);
            $materials = array("oak", "beech", "yew");
            if(strpos($item, "arrow shaft") !== false) {
                $log = "oak log";
            }
            else if(strpos($item, "unfinished arrow") !== false) {
                $log = "arrow shaft";
            }
            else {
                $material = strtolower(explode(" ", $POST['item'][0]));
                if(in_array($material, $materials) == false) {
                    $this->gameMessage("ERROR: You are not allowed to fletch from that material", true);
                    return false;
                }
                $log = $material . ' log';
            }
            
    
            $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $log;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You missing one or more items in your inventory", true);
                return false;
            }
        
            $sql = "SELECT wood_required, level, cost FROM armory_items_data WHERE item=:item";
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
            
            if($item === 'unfinished arrow') {
                $item_data = get_item($this->session['inventory'], 'feather');
                if(!$item_data['amount'] < 0 || $item_data['amount'] < $amount) {
                    $this->gameMessage("ERROR: You don't have enough feathers in your inventory", true);
                    return false;    
                }
            }

            $materials_needed = $row2['wood_required'] * $amount;
            var_dump($materials_needed);
            if($materials_needed > $row['amount']) {
                $this->gameMessage("ERROR! You dont have enough {$material}", true);
                return false;
            }
            
            $cost = $row2['cost'] * $amount;
            if($this->session['gold'] < $cost) {
                $this->gameMessage("ERROR! You don't have enough gold", true);
                return false;
            }
    
            try {
                $this->db->conn->beginTransaction();
                
                if($item === 'arrow shaft') {
                    // For every log you get 10 arrows
                    $this->UpdateGamedata->updateInventory($item, $amount * 10);    
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, $amount); 
                }
                if($this->session['profiency'] !== 'miner') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory('gold', -$cost);   
                }
                if($item === 'unfinished arrow') {
                    // Update feather/arrow shaft in inventory
                    $this->UpdateGamedata->updateInventory('feather', -$materials_needed);
                    $this->UpdateGamedata->updateInventory('arrow shaft', -$materials_needed, true);
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($log , -$materials_needed, true);
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                if(strpos($e->getMessage, 'inv_amount') !== false) {
                    $this->gameMessage("Your inventory is full!", true);
                }
                else{
                    $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                    $this->gameMessage("ERROR: Something unexpected happened, please try again", true);    
                }
                return false;
            }
            $this->db->closeConn();
        }
    }
?>