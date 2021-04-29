<?php
    class Merchant_model extends model {
        public $username;
        public $session;
        private $locations = array("fagna", "towhar", "golbak",  "krasnur", "tasnobil", "cruendo", "snerpiir",
                                   "hirtam", "fansal-plains", "pvitul", "khanz", "ter",);
        
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
            
            // If trader assignment is not 0 then the player has a trading assignment
            if($data['trader_data']['assignment_id'] != 0 ) {
                $sql4 = "SELECT base, destination, cargo, assignment_amount, assignment_type
                         FROM trader_assignments
                         WHERE assignment_id=:assignment_id";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_INT);
                $param_assignment_id = $data['trader_data']['assignment_id'];
                $stmt4->execute();
                $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
                $data['trader_data']['base'] = $row4['base'];
                $data['trader_data']['destination'] = $row4['destination'];
                $data['trader_data']['cargo'] = $row4['cargo'];
                $data['trader_data']['assignment_amount'] = $row4['assignment_amount'];
                $data['trader_data']['assignment_type'] = $row4['assignment_type'];
            }
            
            $sql = "SELECT date_inserted FROM merchant_offers LIMIT 1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data['merchantTimes'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // If it has been 4 hours, make new trades
            if(date_timestamp_get(new DateTime($data['merchantTimes']['date_inserted'])) + 14400
               < date_timestamp_get(new DateTime(date("Y-m-d h:i:s")))) {
                $this->makeTrades();
            }
            
            // Get trader offers
            $data['merchant_offers'] = $this->getOffers();
            
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
        public function makeTraderAssignments() {
            $small_trades_amount = 5;
            $medium_trades_amount = 3;
            $large_trades_amount = 2;
            $favor_amount = 5;
            
            $trader_assignments = array();
            $favor_index = 0;
            for($i = 1; $i < ($small_trades_amount + $medium_trades_amount + $large_trades_amount + $favor_amount + 1); $i++) {
                $assignment = array();
                switch($i) {
                    case ($i <= $small_trades_amount):
                        // Make assignment with easy difficulty
                        $assignment['assignment_type'] = "small trade";
                        $assignment['assignment_amount'] = rand(60, 100);
                        $assignment['reward'] = 200;
                        $assignment['time'] = 600;
                        break;
                    case ($i <= ($small_trades_amount + $medium_trades_amount)):
                        // Make missions with medium difficulty
                        $assignment['assignment_type'] = "medium trade";
                        $assignment['assignment_amount'] = rand(120, 300);
                        $assignment['reward'] = 500;
                        $assignment['time'] = 500;
                        break;
                    case ($i <= ($small_trades_amount + $medium_trades_amount + $large_trades_amount)):
                        // Make missions with hard difficulty
                        $assignment['assignment_type'] = "large trade";
                        $assignment['assignment_amount'] = rand(350, 700);
                        $assignment['reward'] = 1200;
                        $assignment['time'] = 400;
                        break;
                    case ($i >= ($small_trades_amount + $medium_trades_amount + $large_trades_amount)):
                        // Make favor missions
                        $assignment['assignment_type'] = "favor";
                        $assignment['assignment_amount'] = rand(150, 200);
                        $assignment['reward'] = 0;
                        $assignment['time'] = 0;
                        break;
                }
                if($assignment['assignment_type'] === "favor") {
                    // Slice location array to filter out the locations that doesnt have favor assignments
                    $locations = array_slice($this->locations, 7);
                    $base = $assignment['base'] = $locations[$favor_index];

                    $destinations = array_filter(array_slice($this->locations, 0, 6), function($var) use ($base) {
                        return ($var != $base);
                    });
                    $assignment['destination'] = $destinations[array_rand($destinations)];
                    $favor_index++;
                    $sql = "SELECT name FROM items WHERE in_game = 1 AND store_value > 0 ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                }
                else {
                    $locations = array_slice($this->locations, 0, 6);
                    $base = $assignment['base'] = $locations[array_rand($locations)];
                    $destinations = array_filter($locations, function($var) use ($base) {
                        return ($var != $base);
                    });
                    $assignment['destination'] = $destinations[array_rand($destinations)];
                    $sql = "SELECT name FROM items WHERE in_game = 1 AND store_value > 0 AND trader_assignment_type=:assignment_type
                            ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":assignment_type", $param_assignment_type, PDO::PARAM_STR);
                    $param_assignment_type = $assignment['assignment_type'];
                }
                $stmt->execute();
                $assignment['cargo'] = $stmt->fetch(PDO::FETCH_OBJ)->name;
                $trader_assignments[] = $assignment;
            }
            try {
                $this->db->conn->beginTransaction();
                // Delete old assignments
                $sql = "DELETE FROM trader_assignments";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute();
                // If no rows has been affected, throw error;
                if($stmt->rowCount() === 0) {
                    throw new Exception("No rows deleted from delete query" . __METHOD__);
                }
                // Insert new trades
                $sql = "INSERT INTO trader_assignments (base, destination, cargo, assignment_amount, time, assignment_type)
                        VALUES(:base, :destination, :cargo, :assignment_amount, :time, :assignment_type)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":base", $param_base, PDO::PARAM_STR);
                $stmt->bindParam(":destination", $param_destination, PDO::PARAM_STR);
                $stmt->bindParam(":cargo", $param_cargo, PDO::PARAM_STR);
                $stmt->bindParam(":assignment_amount", $param_assignment_amount, PDO::PARAM_INT);
                $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
                $stmt->bindParam(":assignment_type", $param_assignment_type, PDO::PARAM_STR);
                foreach($trader_assignments as $key => $value) {
                    $param_base = $value['base'];
                    $param_destination = $value['destination'];
                    $param_cargo = $value['cargo'];
                    $param_assignment_amount = $value['assignment_amount'];
                    $param_time = $value['time'];
                    $param_assignment_type = $value['assignment_type'];
                    $stmt->execute();
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->errorHandler->reportError(array($this->username, $e->getMessage()));
                return false;
            }
        }
        public function makeTrades() {
            $location_stores = array();
            $locations = array_slice($this->locations, 1);
            for($i = 0; $i < count($locations); $i++) {
                $location = $locations[$i];
                $db_table_name = $location . '_rate';
                $db_table_name = str_replace("-", "_", $db_table_name);
                
                // Select 4 items which has store_rate of 1 in the specified location
                $sql = "SELECT name, store_value, {$db_table_name} FROM items WHERE {$db_table_name} = 1 AND store_value > 0
                        ORDER BY RAND() LIMIT 4";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute();
                $row1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Select 4 items which has store_rate of 3 in the specified location
                $sql = "SELECT name, store_value, {$db_table_name} FROM items WHERE NOT {$db_table_name} = 1 AND store_value > 0
                        ORDER BY RAND() LIMIT 3";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute();
                $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $items = array_merge($row1, $row2);
                for($x = 0; $x < count($items); $x++) {
                    $random_item = $items[$x];
                    switch(intval($items[$x][$db_table_name])) {
                        case 1:
                            $random_item['amount'] = rand(4,10);
                            break;
                        case 2:
                            $random_item['amount'] = rand(2,7);
                            break;
                        case 3:
                            $random_item['amount'] = rand(1,5);
                            break;
                        case 4:
                            $random_item['amount'] = rand(1,2);
                            break;
                        default:
                            $random_item['amount'] = 0;
                            break;
                    }
                    $random_item['store_rate'] = $items[$x][$db_table_name];
                    // First add the store_rate variable to price, decimal value
                    $random_item['price'] = floor($random_item['store_value'] *
                         (1 + ($random_item['store_rate'] / 30)));
            
                    // If there are few items add extra to the price
                    $random_item['price'] = floor($random_item['price'] + 
                                                 ($random_item['price'] / 50 * (1 - ($random_item['amount'] * 0.10)))
                                                 + rand($random_item['store_rate'] * 0.05
                                                 , $random_item['store_rate'] * 0.10));
                    $location_stores[$location][] = $random_item;
                }
            }
            try {
                $this->db->conn->beginTransaction();
                
                // Delete old trades
                $sql = "DELETE FROM merchant_offers";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute();
                // If no rows has been affected, throw error;
                if($stmt->rowCount() === 0) {
                    throw new Exception("No rows deleted from delete query " . __METHOD__);
                }
                
                // Insert new trades
                $sql = "INSERT INTO merchant_offers (location, item, price, amount)
                        VALUES(:location, :item, :price, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":price", $param_price, PDO::PARAM_INT);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                foreach($location_stores as $key => $value) {
                    $param_location = $key;
                    for($i = 0; $i < count($value); $i++) {
                        $param_item = $value[$i]['name'];
                        $param_price = $value[$i]['price'];
                        $param_amount = $value[$i]['amount'];
                        $stmt->execute();
                    }
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->errorHandler->reportError(array($this->username, $e->getMessage()));
                return false;
            }
        }
        public function getMerchantCountdown($js = false) {
            $sql = "SELECT date_inserted FROM merchant_offers LIMIT 1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo $date = date_timestamp_get(new DateTime($row['date_inserted']));
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
                    $new_merchant_buy_price = $row['price'];
                    $total_price = 0;
                    for($i = 0; $i < $amount; $i++) {
                        $new_merchant_buy_price -= $reduction_price;
                        $total_price += $new_merchant_buy_price;
                    }
                    $new_merchant_buy_price = floor($new_merchant_buy_price);
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
                $new_merchant_sell_price;
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
                    $param_price = $new_merchant_buy_price;
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