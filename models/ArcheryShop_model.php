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
            $items = implode('|', array("oak", "spruce", "birch", "yew", "unfinished arrow", "arrow shaft"));
            $sql = "SELECT item, wood_required, cost, type FROM armory_items_data
                    WHERE item REGEXP '{$items}' ORDER BY type DESC, cost ASC";
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
            $materials = array("oak", "birch", "yew");
            if(strpos($item, "arrow shaft") !== false) {
                $log = "oak log";
            }
            else if(strpos($item, "unfinished arrow") !== false) {
                $log = "arrow shaft";
            }
            else {
                $material = strtolower(explode(" ", $POST['item'][0]));
                if(in_array($material, $materials) == false) {
                    $this->response->addTo("errorGameMessage", "You are not allowed to fletch from that material");
                    return false;
                }
                $log = $material . ' log';
            }
            $param_item = $log;
            $param_username = $this->username;
            $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You missing one or more items in your inventory");
                return false;
            }
        
            $param_item = $item;
            $sql = "SELECT wood_required, level, cost FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "That item does not exist!");
                return false;
            }
            if($row2['level'] > $this->session['miner']['level']) {
                $this->response->addTo("errorGameMessage", "Your level is too low");
                return false;
            }
            
            if($item === 'unfinished arrow') {
                $item_data = get_item($this->session['inventory'], 'feather');
                if(!$item_data['amount'] < 0 || $item_data['amount'] < $amount) {
                    $this->response->addTo("errorGameMessage", "You don't have enough feathers in your inventory");
                    return false;    
                }
            }

            $materials_needed = $row2['wood_required'] * $amount;
            if($materials_needed > $row['amount']) {
                $this->response->addTo("errorGameMessage", "You dont have enough {$material}");
                return false;
            }
            
            $cost = $row2['cost'] * $amount;
            if($this->session['gold'] < $cost) {
                $this->response->addTo("errorGameMessage", "You don't have enough gold");
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
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
        }
    }
?>