<?php
    class Merchant_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData($js = false) {
            if($this->session['location'] === 'travelling') {
                header("Location: /city");
                exit();
            }
            
            $cities = array('towhar', 'golbak', 'snerpiir', 'krasnur', 'tasnobil', 'cruendo', 'fagna');
            if(in_array($this->session['location'], $cities) == false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }

            $data = array();
            $sql = "SELECT item, {$this->session['location']}, cost FROM merchants";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_amount = 0;
            $stmt->execute();
            $data['city'] = $this->session['location'];
            $data['shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT assignment_amount, assignment_id, cart, delivered,
                    (SELECT capasity FROM travelbureau_carts WHERE wheel= cart) as capasity FROM trader
                     WHERE username=:username";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $data['trader_data'] = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            
            if($data['trader_data']['assignment_id'] != 0 ) {
                $sql4 = "SELECT base, destination, cargo, cargo_amount FROM trader_assignments WHERE assignment_id=:assignment_id";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $data['trader_data']['assignment_id'];
                $stmt4->execute();
                $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
                array_push($data['trader_data'], $row4);
            }
            
            $sql5 = "SELECT assignment_id, base, destination, cargo, cargo_amount, time, reward, assignment_type FROM trader_assignments
                     WHERE base=:base";
            $stmt5 = $this->conn->prepare($sql5);
            $stmt5->bindParam(":base", $param_city, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $stmt5->execute();
            $data['trader_assignments'] = $stmt5->fetchAll(PDO::FETCH_ASSOC);
            
            $data['gold'] = $this->session['gold'];
            $this->closeConn();
            // if statement to check if ajax request is being called
            if($js === true) {
                get_template('merchantStock', $data, true);
            }
            else {
                return $data;
            }
        }
        
        public function buyItem($item, $quantity) {
            $gold = get_item($this->session['inventory'], 'gold');
            $city = $this->session['location'];
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($city, $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again later!", true);
                return false;
            }
            $sql = "SELECT $city, cost FROM merchants WHERE item=:item";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $dbItem = $row[$city];
            if($gold['amount'] < $row['cost']) {
                $this->gameMessage("ERROR: You don't have enough gold", true);
                return false;
            }
            if($dbItem <= 0) {
                $this->gameMessage("ERROR: There isn't any left of this item", true);
                return false;
            }

            $newvalue = $dbItem - $quantity;
            $price = $quantity * $row['cost'];
            
            //Check if item exists in users stockpile
            try {
                $this->conn->beginTransaction();
                update_inventory($this->conn, $this->username, $item, $quantity);
                update_inventory($this->conn, $this->username, 'gold', -$price);
            
                //Update merchant
                $sql3 = "UPDATE merchants SET $city=:quant WHERE item=:item";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":quant", $param_newValue, PDO::PARAM_STR);
                $stmt3->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_newValue = $newvalue;
                $param_item = $item;
                $stmt3->execute();
            
                $this->conn->commit();
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            get_inventory($this->conn, $this->username);
        }
    }
?>