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
        public function makeMeal($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to make food dish
            
            $type = $POST['item'];
            $amount = $POST['amount'];
            $sql = "SELECT item_id, cost, food_units FROM bakery_data WHERE type=:type";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $param_type = $type;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The product you are trying to make doesnt exists!", true);
                return false;
            }
            if($this->session['gold'] < $row['cost'] * $amount) {
                $this->gameMessage("ERROR: You don't have enough gold!", true);
                return false;
            }
            
            $sql = "SELECT ingredient, amount FROM bakery_ingredients WHERE item_id=:item_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item_id", $param_type_id, PDO::PARAM_STR);
            $param_type_id = $row['item_id'];
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
                $this->gameMessage("Error! You don't have enough of $item", true);
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
                    $this->gameMessage("Error! You don't have enough of " . $key['item'], true);
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
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>