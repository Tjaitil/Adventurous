<?php
    class stockpile_model extends model {
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
        
        public function getStockpile($js = false) {
            $sql = "SELECT amount, item FROM stockpile WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data = array();
            $data['stockpile'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            if($js === true) {
                get_template('stockpile', $data, true);
            }
            else {
                return $data;
            }
        }
        public function updateInventory($insert, $item, $quantity) {
            //$insert, 1 = insert, 0 = widthdraw
            $sql = "SELECT amount FROM stockpile WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0 && $insert === '0') {
                $this->gameMessage("ERROR: You don't have that item", true);
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
            if(!$stmt2->rowCount() > 0 && $insert === '1') {
                $this->gameMessage("ERROR: You don't have that item", true);
                return false;
            }
            if($quantity === 'all' && $insert === '1') {
                $quantity = $row2['amount'];
            }
            else if ($quantity === 'all' && $insert === '0') {
                $quantity = $row['amount'];
            }
            if($insert === '0' && $quantity > $row['amount']) {
                $this->gameMessage("ERROR: You don't have that amount to widthdraw", true);
                return false;
            }
            if($insert === '1' && $quantity > $row2['amount']) {
                $this->gameMessage("ERROR: You don't have that amount to insert", true);
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                if($insert === "0") {
                    //widthdraw from stockpile
                    $this->updateStockpile($item, -$quantity);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, $quantity, true);
                }
                else  if($insert === "1" ) {
                    //Insert into stockpile
                    $this->updateStockpile($item, $quantity);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -$quantity, true);
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile()(), $e->getLine()(), $e->getMessage()());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
        private function updateStockpile($item, $quantity) {
            $sql = "SELECT amount FROM stockpile WHERE item=:item AND username=:username";  
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            if ($count === 0 && $quantity > 0) {
                // Insert new item into bank
                $sql = "INSERT INTO stockpile (username, item, amount) VALUES(:username, :item, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $param_username = $this->username;
                $param_item = $item;
                $param_amount = $quantity;
                $stmt->execute();
    
            }
            $new_amount = $row['amount'] + $quantity;
            
            if($count > 0 && $new_amount > 0) {
                // If items already exists in bank
                $sql2 = "UPDATE stockpile SET amount=:amount WHERE username=:username AND item=:item";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_amount = $row['amount'] + $quantity;
                $param_username = $this->username;
                $param_item = $item;
                $stmt2->execute();
            }
            if(!$new_amount > 0) {
                //If item is zero
                $sql = "DELETE FROM stockpile WHERE item=:item AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $this->username;
                $stmt->execute();
            }
        }
    }
?>