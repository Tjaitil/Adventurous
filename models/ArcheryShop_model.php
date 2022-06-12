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
        public function getData($js = false) {
            $data = array();
            $sql = "SELECT a.item_id, a.item, a.price, a.type, b.material, b.required_amount
                    FROM armory_items_data AS a INNER JOIN 
                    archery_shop_data AS b ON a.item_id=b.item_id
                    ORDER BY type DESC, price ASC";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data = array();

            $setAmountItems = array("arrow shaft");
            foreach($rows as $key => $value) {
                $index = array_search($value['item_id'], array_column($data, 'item_id'));
                if($index !== false) {
                    $data[$index]['required'][] = 
                        array("required_amount" => $value['required_amount'], "material" => $value['material']);
                } else {
                    $value['required'][] = array("required_amount" => $value['required_amount'], "material" => $value['material']);
                    (array_search($value['item'], $setAmountItems) !== false) ? $value['setAmount'] = 15 : "";
                    array_push($data, $value);
                }
            }

            if($js === false) {
                return $data;
            } else {
                $this->response->addTo("data", $data, array("index" => "data"));
            }
        }
        public function fletch($POST) {
            $item = strtolower($POST['item']);
            $amount = $POST['amount'];

            $param_item = $item;
            $sql = "SELECT a.item_id, a.item, a.price, a.type, b.material, b.required_amount
                FROM armory_items_data AS a INNER JOIN 
                archery_shop_data AS b ON a.item_id=b.item_id WHERE a.item=:item
                ORDER BY type DESC, price ASC";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You cannot craft this item!");
                return false;
            }
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $have_required_items = true;
            $check_item = 'none';
            foreach($data as $key) {
                $check_item = get_item($this->session['inventory'], $key['material']);
                if(is_null($check_item) || $check_item['amount'] < $key['required_amount']) {
                    $have_required_items = false;
                    $check_item = $key['material']; 
                    break;
                }
            }
            if($have_required_items != true) {
                $this->response->addTo("errorGameMessage", "You don't have enough of $check_item");
            }

            $param_item = $item;
            $sql = "SELECT wood_required, level, price FROM armory_items_data WHERE item=:item";
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
                        
            $cost = $row2['price'] * $amount;
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
                // Subtract gold from inventory
                $this->UpdateGamedata->updateInventory('gold', -$cost);   
                foreach($data as $key) {
                    $this->UpdateGamedata->updateInventory($key['material'], -$key['required_amount'] * $amount);
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