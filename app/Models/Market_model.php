<?php
    class Market_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData($js = false) {
            //Function to gather data
            $data = array();
            $param_username = $this->username;
            $sql = "SELECT id, offeror, item, amount_left, price_ea, type FROM offers
                    WHERE NOT offeror=:username AND amount_left > 0";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data['offers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT id, type, item, amount, price_ea, progress, box_item, box_amount
            FROM offers WHERE offeror=:username";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $data['my_offers'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT id, type, item, amount, price_ea FROM offer_records WHERE username=:username LIMIT 10";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['history'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
            if($js == false) {
                return $data;
            }
            else {
                switch ($js) {
                    case '1':
                        get_template('offers', $data['offers'], true);
                        echo "#";
                        get_template('myOffers', $data['my_offers'], true);
                        break;
                    case '2':
                        get_template('offers', $data['offers'], true);
                        echo "#";
                        get_template('myOffers', $data['my_offers'], true);
                        echo "#";
                        get_template('history', $data['history'], true);
                        break;
                    case '3':
                        get_template('myOffers', $data['my_offers'], true);
                        break;
                }
            }
        }
        public function getOffers() {
            
        }
        public function newOffer($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from stockpile.js
            // Function to create new offer from player input in market.php
            
            $post_data = json_decode($POST['JSON_data'], true);
            $post_data['item'] = strtolower($post_data['item']);
            $param_username = $this->username;
            $sql  ="SELECT item FROM offers WHERE offeror=:offeror";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":offeror", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() + 1 > 6) {
                $this->response->addTo("errorGameMessage", "You can only have 6 offers");
                return false;
            }
            
            if($post_data['offerType'] == 'Sell') {
                $item = get_item($this->session['inventory'], $post_data['item']);
                if($item == null) {
                    $this->response->addTo("errorGameMessage", "You don't have the item you are currently trying to sell");
                    return false;
                }
                /*$row = $stmt->fetch(PDO::FETCH_ASSOC);*/
                if($item['amount'] < $post_data['amount']) {
                    $this->response->addTo("errorGameMessage", "You don't have that many to sell");
                    return false;
                }
                
                /*$sql = "SELECT item FROM offers WHERE username=:username AND type='sell'";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if($stmt->rowCount() > 6) {
            
                }*/
            }
            else if($post_data['offerType'] == 'Buy') {
                $sql = "SELECT amount FROM inventory WHERE item='gold' AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage", "You don't have any gold");
                    return false;
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['amount'] < $post_data['price']) {
                    $this->response->addTo("errorGameMessage", "You don't have enough gold");
                    return false;
                }
            }
            try {
                $this->db->conn->beginTransaction();
                
                $param_username = $this->username;
                $param_type = $post_data['offerType'];
                $param_item = $post_data['item'];
                $param_amount = $post_data['amount'];
                $param_price_ea = $post_data['price'];
                $param_amount_left = $post_data['amount'];
                $sql = "INSERT INTO offers (offeror, type, item, amount, price_ea, amount_left)
                        VALUES (:username, :type, :item, :amount, :price_ea, :amount_left)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->bindParam(":price_ea", $param_price_ea, PDO::PARAM_STR);
                $stmt->bindParam(":amount_left", $param_amount_left, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_id = $this->db->conn->lastInsertId();
                if($post_data['offerType'] === 'Sell') {
                    $param_item = $post_data['item'];
                    $param_amount = $post_data['amount'];
                }
                else if($post_data['offerType'] === 'Buy') {
                    $param_item = 'gold';
                    $param_amount = $post_data['price'] * $post_data['amount'];
                }
                $sql2 = "INSERT INTO escrow (id, offeror, item, amount)
                         VALUES (:id, :username, :item, :amount)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                
                $stmt2->execute();
                
                if($post_data['offerType'] == 'Sell') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($param_item, -$param_amount, true);
                }
                else if($post_data['offerType'] == 'Buy') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory('gold', -$param_amount * $param_price_ea, true);
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
        }
        public function trade($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from stockpile.js
            // Function to trade items with other players
            $id = $POST['id'];
            $amount = $POST['amount'];
            
            $param_id = $id;
            $sql = "SELECT offeror, id, type, item, price_ea, amount_left, progress, box_amount FROM offers WHERE id=:id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "That offer is no longer available");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //$row['offeror'] is the person who has made the offer
            if($row['offeror'] === $this->username) {
                $this->response->addTo("errorGameMessage", "You don't need to trade with yourself");
                return false;
            }
            if($row['amount_left'] < $amount) {
                $this->response->addTo("errorGameMessage", "The person isn't selling that many");
                return false;
            }
            
            //If the offeror is buying
            if($row['type'] === 'Buy') {
                $param_item = $row['item'];
                $param_username = $this->username;
                $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMesssage", "You don't have that item in your inventory");
                    return false;
                }
                if($inventory['amount'] <  $amount) {
                    $this->response->addTo("errorGameMessage", "You selected amount doesn't reflect the amount in your inventory");
                    return false;
                }
            }
            // If the offeror is selling
            else if($row['type'] === 'Sell') {
                $param_username = $this->username;
                $sql = "SELECT amount FROM inventory WHERE item='gold' AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage", "You don't have any gold in your inventory");
                    return false;
                }
                $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
                if($inventory['amount'] < ($row['price_ea'] * $amount)) {
                    $this->response->addTo("errorGameMessage", "You don't have enough gold to buy this item");
                    return false;
                }
            }
            $progress = $amount;
            $new_amount = $row['amount_left'] - $amount;
            $cost = $amount * $row['price_ea'];
            
            $sql = "SELECT week_price, week_amount FROM item_prices WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $row['item'];
            $stmt->execute();
            $row_count = $stmt->rowCount();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['type'] === 'Buy') {
                $offeror['item'] = $row['item'];
                $offeror['amount'] = $amount;
                $user_type = 'Sell';
            }
            else {
                $offeror['item'] = 'gold';
                $offeror['amount'] = $cost;
                $user_type = 'Buy';
            }
            //For update_records function
            $row['amount'] = $amount;
            try {
                $this->db->conn->beginTransaction();
            
                //Update this->username
                if($row['type'] === 'Buy') {
                    // Send message    
                    
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row['item'], -$amount);
                    $this->UpdateGamedata->updateInventory('gold', $cost, true);
                    
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row['item'], $amount);
                    $this->UpdateGamedata->updateInventory('gold', -$cost, true);
                }
                        
                
                if(!$row_count > 0) {
                    $sql = "INSERT INTO item_prices (item) VALUES (:item)";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_item = $row['item'];
                    $stmt->execute();
                }
                
                $param_progress = $row['progress'] + $progress;
                $param_amount_left = $new_amount;
                $param_box_item = $offeror['item'];
                $param_box_amount = $offeror['amount'] + $row['box_amount'];
                $param_id = $id;
                $param_username = $row['offeror'];
                $sql = "UPDATE offers SET progress=:progress, amount_left=:amount_left, box_item=:box_item, box_amount=:box_amount
                        WHERE id=:id AND offeror=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":progress", $param_progress, PDO::PARAM_INT);
                $stmt->bindParam(":amount_left", $param_amount_left, PDO::PARAM_INT);
                $stmt->bindParam(":box_item", $param_box_item, PDO::PARAM_STR);
                $stmt->bindParam(":box_amount", $param_box_amount, PDO::PARAM_INT);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                if($new_amount > 0) {
                    $param_amount  = ($row['type'] === 'Buy') ? ($row['price_ea'] * $row['amount_left']) - $row['price_ea'] : $new_amount;
                    $param_id = $id;
                    $param_offeror = $row['offeror'];
                    $sql = "UPDATE escrow SET amount=:amount WHERE id=:id AND offeror=:offeror";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                    $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                    $stmt->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                    $stmt->execute();
                }
                else if($new_amount == 0) {
                    $param_offeror = $row['offeror'];
                    $param_id = $id;
                    $sql2 = "DELETE FROM escrow WHERE id=:id AND offeror=:offeror";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                    $stmt2->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                    $stmt2->execute();
                }
                
                $param_week_amount = $row2['week_amount'] + $amount;
                $param_week_price = $row2['week_price'] + $cost;
                $param_item = $row['item'];
                $sql2 = "UPDATE item_prices SET week_amount=:week_amount, week_price=:week_price WHERE item=:item";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":week_amount", $param_week_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":week_price", $param_week_price, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt2->execute();
                
                $sql3 = "UPDATE escrow SET amount=:amount WHERE id=:id AND offeror=:offeror";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt3->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt3->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                $param_offeror = $row['offeror'];
                $param_id = $id;
                $param_amount = $row['price_ea'] * $new_amount;
                $stmt3->execute();
                
                //Update both offer_records for both the buyer and the seller
                $this->update_records($row['offeror'], $row, $row['type']);
                //Make sure the offer type is different for the user trading than the offeror
                $this->update_records($this->username, $row, $user_type);
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            echo "finished trade";
        }
        private function update_records($username, $offer_info, $type) {
            $param_id = $offer_info['id'];
            $param_username = $username;
            $sql = "SELECT (SELECT COUNT(amount) FROM offer_records WHERE username=:username) as count, username, amount
                    FROM offer_records WHERE id=:id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['count'] > 11) {
                $sql = "DELETE FROM offer_records WHERE username=:username ORDER BY time DESC LIMIT 1";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
            }
            if(!$stmt->rowCount() > 0) {
                $param_id = $offer_info['id'];
                $param_username = $username;
                $param_type = $type;
                $param_item = $offer_info['item'];
                $param_amount = $offer_info['amount_left'];
                $param_price_ea = $offer_info['price_ea'];
                $sql = "INSERT INTO offer_records (id, username, type, item, amount, price_ea)
                        VALUES (:id, :username, :type, :item, :amount, :price_ea)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->bindParam(":price_ea", $param_price_ea, PDO::PARAM_STR);
                $stmt->execute();
            }
            else {
                $param_amount = $offer_info['amount'] + $row['amount'];
                $param_id = $offer_info['id'];
                $param_username = $username;
                $sql = "UPDATE offer_records SET amount=:amount WHERE id=:id AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
            }
        }
        public function cancelOffer($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from stockpile.js
            // Function to cancel offer from market
            $id = $POST['id'];
            
            $param_id = $id;
            $param_username = $this->username;
            $sql = "SELECT type, item, amount, price_ea FROM offers WHERE id=:id AND offeror=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "The offer is already completed or doesn't exist");
                return false;
            }
            
            $sql =  "SELECT item, amount FROM escrow WHERE id=:id AND offeror=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_id, $param_username already defined in statement 1
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "DELETE FROM offers WHERE id=:id AND offeror=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $id;
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "DELETE FROM escrow WHERE id=:id AND offeror=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $id;
                $param_username = $this->username;
                $stmt2->execute();                
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($row2['item'], $row2['amount'], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
        }
        public function fetchItem($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from stockpile.js
            // Function to fetch item from a completed offer into inventory
            $id = $POST['id'];
            
            $param_id = $id;
            $param_username = $this->username;
            $sql = "SELECT id, amount_left, box_item, box_amount FROM offers WHERE id=:id AND offeror=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You dont have a compeleted offer on that item");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                if($row['amount_left'] == 0) {
                    $sql = "DELETE FROM offers WHERE id=:id AND offeror=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_id = $id;
                    $param_username = $this->username;
                    $stmt->execute();
                }
                else {
                    $sql = "UPDATE offers SET box_amount=0 WHERE id=:id AND offeror=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":id", $param_id, PDO::PARAM_INT);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_id = $id;
                    $param_username = $this->username;
                    $stmt->execute();   
                }
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($row['box_item'], $row['box_amount'], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
        }
        public function searchOffers($item) {
            //AJAX function, returns a array with offers that matches item search provided by user
            $param_item = "%{$item}%";
            $param_username = $this->username;
            $sql = "SELECT id, offeror, item, amount_left, price_ea, type FROM offers WHERE item LIKE :item AND offeror !=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                return;
            }
            else {
                get_template('offers', $row, true);
            }
        }
    }
?>