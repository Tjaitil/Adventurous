<?php
    class Bakery_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function make($type, $amount) {
            $sql = "SELECT item_id, cost, food_units FROM bakery_data WHERE type=:type";
            $stmt = $this->conn->prepare($sql);
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
            
            $sql = "SELECT ingredient, amount FROM bakery_ingredients WHERE item_id";
            $stmt = $this->conn->prepare($sql);
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
            
            /*$value = array();
            for($i = 0; $i < count($ingredients) / 2; $i++) {
                array_push($items, $ingredients[$i*2]);
            }

            $items[] = $this->username;
            
            $in  = str_repeat('?,', count($items) - 2) . '?';
            $sql = "SELECT item, amount FROM inventory WHERE item IN ($in) AND username= ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($items);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $x = 0;
            foreach($row2 as $key) {
                var_dump(intval($key['amount']));
                var_dump($ingredients[$x*2 + 1] * $amount);
                if(intval($key['amount']) < $ingredients[$x*2 + 1] * $amount) {
                    $this->gameMessage("Error! You don't have enough of " . $key['item'], true);
                    return false;
                }
                $x++;
            }*/
            
            try {
                $this->conn->beginTransaction();
            
                for($i = 0; $i<count($row2); $i++) {
                    update_inventory($this->conn, $this->username, $row2[$i]['ingredient'], -$row2[$i]['amount'] * $amount);
                }
                update_inventory($this->conn, $this->username, $type, $amount);
                update_inventory($this->conn, $this->username, 'gold', -$row['cost'] * $amount, true);
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
        }
    }
?>