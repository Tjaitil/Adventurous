<?php
    class stockpile_model extends model {
        public $username;
        public $session;
        private $insert_stockpile = false;
        private $insert_inventory = false;
        
        function __construct($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getStockpile($js = false) {
            $sql = "SELECT amount, item FROM stockpile WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data = array();
            $data['stockpile'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
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
            $stmt = $this->conn->prepare($sql);
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
            $stmt2 = $this->conn->prepare($sql2);
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
                $this->conn->beginTransaction();
                if($insert === "0") {
                    //widthdraw from stockpile
                    update_stockpile($this->conn, $this->username, $item, -$quantity);
                    update_inventory($this->conn, $this->username, $item, $quantity, true);
                }
                else  if($insert === "1" ) {
                    //Insert into stockpile
                    update_stockpile($this->conn, $this->username, $item, $quantity);
                    update_inventory($this->conn, $this->username, $item, -$quantity, true);
                }
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