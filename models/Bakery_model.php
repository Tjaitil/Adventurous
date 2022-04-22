<?php
    class Bakery_model extends model {
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
            $sql = "SELECT item_id, type, cost, food_units FROM bakery_data WHERE bakery_item=1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $item_ids = array_column($data, 'item_id');
            
            $in  = str_repeat('?,', count($item_ids) - 1) . '?';
            $sql = "SELECT item_id, ingredient, amount FROM bakery_ingredients WHERE item_id IN ($in)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($item_ids);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i = 0; $i < count($rows); $i++) {
                for($x = 0; $x < count($data); $x++) {
                    if($data[$x]['item_id'] === $rows[$i]['item_id']) {
                        unset($rows[$i]['item_id']);
                        $data[$x]['ingredients'][] = $rows[$i];
                        break;
                    }
                }
            }
            return $data;
        }
        public function makeMeal($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to make food dish
            
            $type = $POST['item'];
            $amount = $POST['amount'];
            $param_type = $type;
            $sql = "SELECT item_id, cost, food_units FROM bakery_data WHERE type=:type";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If profiency is farmer cut the cost by 75%
            if($this->session['profiency'] === 'farmer') {
                $row['cost'] *= 0.25; 
            }
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "The product you are trying to make doesnt exists!");
                return false;
            }
            if($this->session['gold'] < $row['cost'] * $amount) {
                $this->response->addTo("errorGameMessage", "You don't have enough gold!");
                return false;
            }
            
            $param_type_id = $row['item_id'];
            $sql = "SELECT ingredient, amount FROM bakery_ingredients WHERE item_id=:item_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item_id", $param_type_id, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $status = true;
            $item = 'none';
            foreach($row2 as $key) {
                $item = get_item($this->session['inventory'], $key['ingredient']);
                if($item['amount'] < $key['amount']) {
                    $status = false;
                    $item = $key['ingredient']; 
                    break;
                }
            }
            if($status != true) {
                $this->response->addTo("errorGameMessage", "You don't have enough of $item");
            }
            
            /*$items = array();
            for($i = 0; $i < count($row2) / 2; $i++) {
                array_push($items, $row2[$i + 1]);
            }

            $items[] = $this->username;
            
            $in  = str_repeat('?,', count($items) - 2) . '?';
            $sql = "SELECT item, amount FROM inventory WHERE item IN ($in) AND username= ?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($items);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $x = 0;
            foreach($row2 as $key) {
                if(intval($key['amount']) < $ingredients[$x*2 + 1] * $amount) {
                    $this->response->addTo("errorGameMessage", "You don't have enough of " . $key['item']);
                    return false;
                }
                $x++;
            }*/
            try {
                $this->db->conn->beginTransaction();
            
                for($i = 0; $i<count($row2); $i++) {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row2[$i]['ingredient'], -$row2[$i]['amount'] * $amount);
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($type, $amount);
                $this->UpdateGamedata->updateInventory('gold', -$row['cost'] * $amount, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
        }
        public function getPrices() {
            $sql = "SELECT name, store_value FROM items WHERE name IN ()";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $item_ids = array_column($data, 'item_id');

        }
    }
?>