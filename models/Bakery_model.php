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
        public function getData($js = false) {
            $data = array();
            $sql = "SELECT b.item_id, b.item, b.price, b.food_units, BE.amount, BE.ingredient 
                    FROM  
                    bakery_data AS B INNER JOIN bakery_ingredients AS BE ON b.item_id = be.item_id 
                    WHERE b.bakery_item=1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $data = array();

            foreach($rows as $key => $value) {
                $index = array_search($value['item_id'], array_column($data, 'item_id'));
                if($index !== false) {
                    $data[$index]['ingredients'][] = 
                        array("amount" => $value['amount'], "ingredient" => $value['ingredient']);
                } else {
                    $value['ingredients'][] = array("amount" => $value['amount'], "ingredient" => $value['ingredient']);
                    if($this->session['profiency'] === 'farmer') {
                        $value['discount'] = $value['price'] * 0.25;
                    }
                    array_push($data, $value);
                }
            }

            if($js === false) {
                return $data;
            } else {
                $this->response->addTo("data", $data, array("index" => "data"));
            }
        }
        public function makeMeal($POST) {
            
            $type = $POST['item'];
            $amount = $POST['amount'];
            $param_type = $type;
            $sql = "SELECT item_id, price, food_units FROM bakery_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_type, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If profiency is farmer cut the price by 75%
            if($this->session['profiency'] === 'farmer') {
                $row['price'] *= 0.25; 
            }
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "The product you are trying to make doesnt exists!");
                return false;
            }
            if($this->session['gold'] < $row['price'] * $amount) {
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
                if(is_null($item) || $item['amount'] < $key['amount']) {
                    $status = false;
                    $item = $key['ingredient']; 
                    break;
                }
            }
            if($status != true) {
                $this->response->addTo("errorGameMessage", "You don't have enough of $item");
            }
            try {
                $this->db->conn->beginTransaction();
            
                for($i = 0; $i<count($row2); $i++) {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row2[$i]['ingredient'], -$row2[$i]['amount'] * $amount);
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($type, $amount);
                $this->UpdateGamedata->updateInventory('gold', -$row['price'] * $amount, true);
                
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