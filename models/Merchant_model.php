<?php
    class Merchant_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData($js = false) {
            if($this->session['location'] === 'travelling') {
                header("Location: /city");
                exit();
            }

            $data = array();
            $data['city'] = $this->session['location'];
            
            $sql2 = "SELECT assignment_id, cart, cart_amount, delivered,
                    (SELECT capasity FROM travelbureau_carts WHERE wheel= cart) as capasity FROM trader
                     WHERE username=:username";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $data['trader_data'] = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            
            if($data['trader_data']['assignment_id'] != 0 ) {
                $sql4 = "SELECT base, destination, cargo, assignment_amount, assignment_type
                         FROM trader_assignments
                         WHERE assignment_id=:assignment_id";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $data['trader_data']['assignment_id'];
                $stmt4->execute();
                $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
                array_push($data['trader_data'], $row4);
            }
            $data['merchant_offers'] = $this->getOffers();
            $sql = "SELECT date FROM merchant_offer_times WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['merchantTimes'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql5 = "SELECT assignment_id, base, destination, cargo, assignment_amount, time, assignment_type FROM trader_assignments
                     WHERE base=:base";
            $stmt5 = $this->db->conn->prepare($sql5);
            $stmt5->bindParam(":base", $param_city, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $stmt5->execute();
            $data['trader_assignments'] = $stmt5->fetchAll(PDO::FETCH_ASSOC);
            
            $data['gold'] = $this->session['gold'];
            $this->db->closeConn();
            // if statement to check if ajax request is being called
            if($js === true) {
                get_template('merchantStock', $data, true);
            }
            else {
                return $data;
            }
        }
        public function getMerchantCountdown($js = false) {
            $sql = "SELECT date FROM merchant_times WHERE location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $date = date_timestamp_get(new DateTime($row['date']));
            $this->db->closeConn();
        }
        public function getOffers($js = false) {
            $sql = "SELECT item, price, amount FROM merchant_offers WHERE location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($js === true) {
                get_template('merchantOffers', $row, true);
                $this->db->closeConn();
            }
            else {
                return $row;
            }
        }
        public function tradeItem($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from merchant.js
            // Function to trade item with merchant
            $item = strtolower($POST['item']);
            $amount = $POST['amount'];
            $mode = $POST['mode'];
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again later!", true);
                return false;
            }
            if($this->session['location'] != 'fagna') {
                $sql = "SELECT item, price, amount FROM merchant_offers WHERE location=:location AND item=:item";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_location = $this->session['location'];
                $param_item = $item;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // If the item doesn't exists and the method is sell
                if($stmt->rowCount() == 0) {
                    if($mode == 'sell') {
                        $this->gameMessage("ERROR: The merchant isn't interested in what you are trying to sell", true);
                    }
                    else {
                        $this->gameMessage("ERROR: The merchant isn't selling what you are trying to buy", true); 
                    }
                    $this->gameMessage("ERROR: The merchant isn't interested in what you are trying to sell", true);
                    return false;
                }
                if($mode == 'buy' && $row['amount'] < $amount) {
                    $this->gameMessage("ERROR: The merchant isn't selling the amount you are trying to buy", true);
                    return false;
                }
            }
            if($mode == 'sell') {
               // Check if city is fagna
                if($this->session['location'] == "fagna") {
                    // Find price for fagna;
                }
                else {
                    // 3% reduction in price
                    if($row['price'] > 1500) {
                        $reduction_price = $row['price'] * 0.05;
                    }
                    else {
                        $reduction_price = $row['price'] * 0.03;
                    }
                    $new_merchant_sell_price = $row['price'];
                    $total_price = 0;
                    for($i = 0; $i < $amount; $i++) {
                        $new_merchant_sell_price -= $reduction_price;
                        $total_price += $new_merchant_sell_price;
                    }
                    $new_merchant_sell_price = floor($new_merchant_sell_price);
                }
                $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $this->username;
                $stmt->execute();
                $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: You don't have item you are trying to selll", true);
                    return false;
                }
                $new_amount = $amount + $row['amount'];
            }
            else {
                // $mode == 'buy'
                $buy_price = $row['price'];
                $new_amount = $row['amount'] - $amount;
                $total_price = $row['price'] * $amount;
            }
            try {
                $this->db->conn->beginTransaction();
                if($mode == 'sell') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -$amount);
                    $this->UpdateGamedata->updateInventory('gold', $total_price, true);
                    
                    $sql = "UPDATE merchant_offers SET price=:price, amount=:amount
                            WHERE location=:location AND item=:item";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":price", $param_price, PDO::PARAM_INT);
                    $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                    $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_price = $new_merchant_sell_price;
                    $param_amount = $new_amount;
                    $param_location = $this->session['location'];
                    $param_item = $item;
                    $stmt->execute();
                }
                else {
                    // $mode == 'buy'
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, $amount);
                    $this->UpdateGamedata->updateInventory('gold', - $total_price, true);
                    
                    $sql = "UPDATE merchant_offers SET amount=:amount
                            WHERE location=:location AND item=:item";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                    $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_amount = $new_amount;
                    $param_location = $this->session['location'];
                    $param_item = $item;
                    $stmt->execute();
                }
            
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->getOffers(true);
            $this->db->closeConn();
        }
        public function insertMerchantOffers($data) {
            
            try {
                $this->db->conn->beginTransaction();
                $sql = "INSERT INTO merchant_offers (username, location, item, price, amount
                        VALUES(:username, :location, :item, :price, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":price", $param_price, PDO::PARAM_INT);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                $param_username = $this->username;
                foreach($location_stores as $key => $value) {
                    $param_location = $key;
                    for($i = 0; $i < count($value); $i++) {
                        $param_item = $value[$i]['item'];
                        $param_price = $value[$i]['price'];
                        $param_amount = $value[$i]['amount'];
                        $stmt->execute();
                    }
                }
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
        public function getPrice($GET) {
            // Function to return sell price from database
            // Called from merchant.js
            
            $sql = "SELECT store_value FROM items WHERE name=:name";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $store_value = $stmt->fetch(PDO::FETCH_OBJ)->store_value;
            echo $store_value;
        }
        public function hello() {
                        $sql = "SELECT amount, want, want_amount FROM merchants WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: This item is not for sale anymore", true);
                return fasle;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $want_item = ($bond == 'false') ? $row['want'] : 'trading bond';
            $item_amount = get_item($this->session['inventory'], $want_item)['amount'];
            if(!$item_amount > 0) {
                $this->gameMessage("ERROR: You don't have any {$want_item}s", true);
                return false;
            }
            else if($item_amount < $row['want_amount']) {
                $this->gameMessage("ERROR: You don't have enough {$want_item}s", true);
                return false;
            }
            //Update merchant
                $sql3 = "UPDATE merchants SET amount=:amount WHERE item=:item";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":amount", $param_newValue, PDO::PARAM_STR);
                $stmt3->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_newValue = $row['amount'] - $amount;
                $param_item = $item;
                $stmt3->execute();
        }
    }
?>