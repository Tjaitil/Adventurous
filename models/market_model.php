<?php
    class market_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData($js = false) {
            $data = array();
            $sql = "SELECT id, offeror, item, amount_left, price_ea, type FROM offers
                    WHERE NOT offeror=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['offers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT id, type, item, amount, price_ea, progress FROM offers WHERE offeror=:username";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $data['my_offers'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT id, type, item, amount, price_ea FROM offer_records WHERE username=:username";
            $stmt3 = $this->conn->prepare($sql3);
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
        
        public function newOffer($post_data) {
            if($post_data['type'] == 'Sell') {
                $item = get_item($this->session['inventory'], $post_data['item']);
                /*$sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $post_data['item'];
                $param_username = $this->username;
                $stmt->execute();*/
                if($item == null) {
                    $this->gameMessage("ERROR: You don't have the item you are currently trying to sell");
                    return false;
                }
                /*$row = $stmt->fetch(PDO::FETCH_ASSOC);*/
                if($item['amount'] < $post_data['amount']) {
                    $this->gameMessage("ERROR: You don't have that many to sell");
                    return false;
                }
            }
            else if($post_data['type'] == 'Buy') {
                $sql = "SELECT amount FROM inventory WHERE item='gold' AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: You don't have any gold");
                    return false;
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['amount'] < $post_data['price']) {
                    $this->gameMessage("ERROR: You don't have enough gold");
                    return false;
                }
            }
            try {
                $this->conn->beginTransaction();
                
                $sql = "INSERT INTO offers (offeror, type, item, amount, price_ea, amount_left)
                        VALUES (:username, :type, :item, :amount, :price_ea, :amount_left)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->bindParam(":price_ea", $param_price_ea, PDO::PARAM_STR);
                $stmt->bindParam(":amount_left", $param_amount_left, PDO::PARAM_STR);
                $param_username = $this->username;
                $param_type = $post_data['type'];
                $param_item = $post_data['item'];
                $param_amount = $post_data['amount'];
                $param_price_ea = $post_data['price'];
                $param_amount_left = $post_data['amount'];
                $stmt->execute();
                
                $sql2 = "INSERT INTO escrow (id, offeror, item, amount)
                         VALUES (:id, :username, :item, :amount)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $param_id = $this->conn->lastInsertId();
                
                if($post_data['type'] === 'Sell') {
                    $param_item = $post_data['item'];
                    $param_amount = $post_data['amount'];
                }
                else if($post_data['type'] === 'Buy') {
                    $param_item = 'gold';
                    $param_amount = $post_data['price'] * $post_data['amount'];
                }
                $stmt2->execute();
                
                if($post_data['type'] == 'Sell') {
                    update_inventory($this->conn, $this->username, $param_item, -$param_amount, true);
                }
                else if($post_data['type'] == 'Buy') {
                    update_inventory($this->conn, $this->username, 'gold', -$param_amount * $param_price_ea, true);
                }
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            $this->closeConn();
        }
        
        public function trade($id, $amount) {
            //AJAX function
            
            $sql = "SELECT offeror, id, type, item, price_ea, amount_left, progress FROM offers WHERE id=:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $param_id = $id;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: That offer is no longer available", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            //$row['offeror'] is the person who has made the offer
            if($row['offeror'] === $this->username) {
                $this->gameMessage("ERROR: You don't need to trade with yourself", true);
                return false;
            }
            if($row['amount_left'] < $amount) {
                $this->gameMessage("ERRORo: The person isn't selling that many");
                return false;
            }
            
            //If the offeror is buying
            if($row['type'] === 'Buy') {
               $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $row['item'];
                $param_username = $this->username;
                $stmt->execute();
                $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: You don't have that item in your inventory", true);
                    return false;
                }
                if($inventory['amount'] <  $amount) {
                    $this->gameMessage("ERROR: You selected amount doesn't reflect the amount in your inventory", true);
                    return false;
                }
            }
            // If the offeror is selling
            else if($row['type'] === 'Sell') {
                $sql = "SELECT amount FROM inventory WHERE item='gold' AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: You don't have any gold in your inventory", true);
                    return false;
                }
                $inventory = $stmt->fetch(PDO::FETCH_ASSOC);
                if($inventory['amount'] < ($row['price_ea'] * $row['amount_left'])) {
                    $this->gameMessage("ERROR: You don't have enough gold to buy this item", true);
                    return false;
                }
            }
            $progress = $amount;
            $new_amount = $row['amount_left'] - $amount;
            $cost = $amount * $row['price_ea'];
            
            $sql = "SELECT week_price, week_amount FROM item_prices WHERE item=:item";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $row['item'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $row['amount'] = $amount;
            try {
                $this->conn->beginTransaction();
                if(!$stmt->rowCount() > 0) {
                    $sql = "INSERT INTO item_prices (item) VALUES (:item)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_item = $row['item'];
                    $stmt->execute();
                }
                if($new_amount > 0) {
                    $sql = "UPDATE offers SET progress=:progress, amount_left=:amount_left WHERE id=:id AND offeror=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":progress", $param_progress, PDO::PARAM_STR);
                    $stmt->bindParam(":amount_left", $param_amount_left, PDO::PARAM_STR);
                    $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_progress = $row['progress'] + $progress;
                    $param_amount_left = $new_amount;
                    $param_id = $id;
                    $param_username = $row['offeror'];
                    $stmt->execute();
                }
                else if($new_amount == 0) {
                    $sql = "DELETE FROM offers WHERE id=:id AND offeror=:offeror";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                    $stmt->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                    $param_id = $id;
                    $param_offeror = $row['offeror'];
                    $stmt->execute();
                    
                    $sql2 = "DELETE FROM escrow WHERE id=:id AND offeror=:offeror";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                    $stmt2->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                    //$param_offeror is already defined in statement 1
                    $param_id = $id;
                    $stmt2->execute();
                }
                if($row['type'] == 'Buy') {
                    //$this->username is seller, offeror is buyer
                    //Update the offeror who is buying
                    update_inventory($this->conn, $row['offeror'], $row['item'], $amount);
                    //Subtract the gold cost of the purchase from buyer
                    update_inventory($this->conn, $this->username, 'gold', $cost);
                    //Updaten $this->username who is selling
                    update_inventory($this->conn, $this->username, $row['item'], -$amount, true);
                }
                else if($row['type'] == 'Sell') {
                    //$this->username is buyer, offeror is seller;
                    //Update $this->username who is buying
                    update_inventory($this->conn, $this->username, $row['item'], $amount);
                    // Subtract the gold cost of the purchase from buyer
                    update_inventory($this->conn, $this->username, 'gold', -$cost);
                    //Update the offeror who is selling
                    update_inventory($this->conn, $row['offeror'], 'gold', $cost, true);
                }
                
                $sql2 = "UPDATE item_prices SET week_amount=:week_amount, week_price=:week_price WHERE item=:item";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":week_amount", $param_week_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":week_price", $param_week_price, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_week_amount = $row2['week_amount'] + $amount;
                $param_week_price = $row2['week_price'] + $cost;
                $param_item = $row['item'];
                $stmt2->execute();
                
                $sql3 = "UPDATE escrow SET amount=:amount WHERE id=:id AND offeror=:offeror";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt3->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt3->bindParam(":offeror", $param_offeror, PDO::PARAM_STR);
                $param_offeror = $row['offeror'];
                $param_id = $id;
                $param_amount = $row['price_ea'] * $new_amount;
                $stmt3->execute();
                
                //Update both offer_records for both the buyer and the seller
                require('../' . constant('ROUTE_HELPER') . 'update_records.php');
                update_records($this->conn, $row['offeror'], $row, $row['type']);
                //Make sure the offer type is different for the user trading than the offeror
                if($row['type'] === 'Sell') {
                    $type = 'Buy';
                }
                else {
                    $type = 'Sell';
                }
                update_records($this->conn, $this->username, $row, $type);
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
        
        public function cancelOffer($id) {
            $sql = "SELECT type, item, amount, price_ea FROM offers WHERE id=:id AND offeror=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_id = $id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The offer is already completed or doesn't exist", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql =  "SELECT item, amount FROM escrow WHERE id=:id AND offeror=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_id, $param_username already defined in statement 1
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "DELETE FROM offers WHERE id=:id AND offeror=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $id;
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "DELETE FROM escrow WHERE id=:id AND offeror=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":id", $param_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $id;
                $param_username = $this->username;
                $stmt2->execute();                
                
                update_inventory($this->conn, $this->username, $row2['item'], $row2['amount'], true);
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