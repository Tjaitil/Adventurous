<?php
    class Stockpile_model extends model {
        public $username;
        public $session;
        protected $insert_stockpile = false;
        protected $insert_inventory = false;
        
        function __construct($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData($js = false) {
            $param_username = $this->username;
            $sql = "SELECT amount, item FROM stockpile WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data = array();
            $data['stockpile'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($js === true) {
                ob_start();
                get_template('stockpile', $data, true);
                $this->response->addTo("html", ob_get_clean());
            }
            else {
                return $data;
            }
            $this->db->closeConn();
        }
        public function updateInventory($POST) {
            // $POST variable holds the post data
            $insert = $POST['insert'];
            $item = strtolower($POST['item']);
            $quantity = $POST['quantity'];
            //$insert, 1 = insert, 0 = widthdraw
            $param_item = $item;
            $param_username = $this->username;
            $sql = "SELECT amount FROM stockpile WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $current_amount = ($row !== false) ? $row['amount'] : 0;
            if($insert === '0' && !$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You don't have that item in stockpile");
                return false;
            }
            $sql2 = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $this->username;
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            if($insert === '1' && !$stmt2->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You don't have that item in inventory");
                return false;
            }
            if($quantity === 'all' && $insert === '1') {
                $quantity = $row2['amount'];
            }
            else if ($quantity === 'all' && $insert === '0') {
                $quantity = $row['amount'];
            }
            if($insert === '0' && $quantity > $row['amount']) {
                $this->response->addTo("errorGameMessage", "You don't have that amount to widthdraw");
                return false;
            }
            if($insert === '1' && $quantity > $row2['amount']) {
                $this->response->addTo("errorGameMessage", "You don't have that amount to insert");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                if($insert === "0") {
                    //widthdraw from stockpile
                    $this->updateStockpile($item, -$quantity, $current_amount);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, $quantity, $current_amount);
                }
                else  if($insert === "1" ) {
                    //Insert into stockpile
                    $this->updateStockpile($item, $quantity, $current_amount);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -$quantity, $current_amount);
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->getData(true);            
        }
        public function updateStockpile($item, $quantity, $current_amount) {
            $param_username = $this->username;
            if ($quantity > 0 && $current_amount === 0) {
                // Insert new item into bank
                $param_item = $item;
                $param_amount = $quantity;
                $sql = "INSERT INTO stockpile (username, item, amount) VALUES(:username, :item, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->execute();
            } else if($quantity - $current_amount === 0) {
                $param_item = $item;
                $param_username = $this->username;
                //If item is zero
                $sql = "DELETE FROM stockpile WHERE item=:item AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
            } else {
                $param_amount = $current_amount + $quantity;
                $param_username = $this->username;
                $param_item = $item;
                // If items already exists in bank
                $sql2 = "UPDATE stockpile SET amount=:amount WHERE username=:username AND item=:item";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt2->execute();
            }
        }
    }
?>