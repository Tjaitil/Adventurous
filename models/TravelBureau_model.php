<?php
    class TravelBureau_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData($js = false) {
            /*$city = $_SESSION['gamedata']['location'];
            /*$sql = "SELECT type, $city, value FROM travelbureau_horses";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_amount = 0;
            $stmt->execute();
            $data = array();
            $data['horse_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data['city'] = $city;*/
            
            $param_username = $this->username;
            $sql = "SELECT cart FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data['cart'] = $stmt->fetch(PDO::FETCH_OBJ)->cart;
            
            $sql = "SELECT wheel, wood, value, capasity, mineral_amount, wood_amount FROM travelbureau_carts"; 
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $data['cart_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            if($js === true) {
                ob_start();
                get_template('cartShop', $data, true);
                $this->response->addTo("html", ob_get_clean());
            }
            else {
                return $data;
            }
        }
        
        public function buyItem($POST) {
            $shop = $POST['shop'];
            $item = $POST['item'];
            $city = $this->session['location'];
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                return false;
            }
            switch ($shop) {
                case 'cart':
                    $selectSTMT1 = "SELECT wheel, wood, $city, mineral_amount, wood_amount FROM travelbureau_carts WHERE wheel=:wheel";
                    $selectBind1 = ":wheel";
                    $param_wheel = $item;
                    $selectSTMT2 = "SELECT wheel, value FROM travelbureau_carts
                                    WHERE wheel = (SELECT trader.cart FROM trader WHERE username=:username)";
                    $updateSTMT1 = "UPDATE trader SET cart=:cart WHERE username=:username";
                    $updateBind1 = ":cart";
                    $updateSTMT3 = "UPDATE travelbureau_carts SET $city=:city WHERE wheel=:wheel";
                break;
                case 'horse':
                    $selectSTMT1 = "SELECT type, $city, value FROM travelbureau_horses WHERE type=:type";
                    $selectBind1 = ":type";
                    $selectSTMT2 = "SELECT value FROM travelbureau_horses WHERE type=
                    (SELECT horse FROM user_data WHERE username=:username)";
                    $param_horse = $item;
                    $updateSTMT1 = "UPDATE user_data SET horse=:horse WHERE username=:username";
                    $updateBind1 = ":horse";
                    $updateSTMT3 = "UPDATE travelbureau_horses SET $city=:city WHERE type=:type";
                break;
             }
            $param = $item;
            $sql = $selectSTMT1;
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam($selectBind1, $param, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            if(!$count > 0) {
                $this->response->addTo("errorGameMessage", "The item you are trying to buy does not exists!");
                return false;
            }
            switch($shop) {
                case 'cart':
                    $mineral = $row['wheel'] . ' bar';
                    $mineral_amount = get_item($this->session['inventory'], $mineral)['amount'];
                    if($mineral_amount < $row['mineral_amount']) {
                        $this->response->addTo("errorGameMessage", "You don't have enough {$mineral}s to buy this!");
                        return false;
                    }
                    $wood = $row['wood'] . ' log';
                    $wood_amount = get_item($this->session['inventory'], $wood)['amount'];
                    if($wood_amount < $row['wood_amount']) {
                        $this->response->addTo("errorGameMessage", "You don't have enough {$wood}s to buy this!");
                        return false;
                    }
                    $boughtMessage = "You bought a {$item} cart for {$row['wood_amount']} {$mineral} and {$row['wood_amount']} {$wood}";
                    break;
                case 'horse':
                    if($row['value'] > $this->session['gold'] && $shop == 'horse') {
                        $this->response->addTo("errorGameMessage", "You don't have enough gold to buy this item!");
                        return false;
                    }
                    break;
                $boughtMessage = "You bought a {$item} horse for {$row['value']} gold";
            }
        
            if(!$row[$city] > 0 ) {
                $this->response->addTo("errorGameMessage", "There are none left of this item in stock");
                return false;
            }
            //Check if user has a horse already, if so 80% of the price will be refunded.
            $param_username = $this->username;
            $sql = $selectSTMT2;
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row_count = $stmt->rowCount();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $newStoreAmount = $row[$city] - 1;
            try {
                $this->db->conn->beginTransaction();
                
                //Update cart to trader/ horse to user
                $sql = $updateSTMT1;
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam($updateBind1, $param, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param = $item;
                $param_username = $this->username;
                $stmt->execute();
                
                //Update gold amount, if $row_count is greater than 0 the user has either cart/horse which is then sold
                switch($shop) {
                    case 'cart':
                        if($row_count != 0) {
                            // Update inventory
                            $this->UpdateGamedata->updateInventory('gold', ($row2['value'] * 0.80));
                        }
                        // Update inventory
                        $this->UpdateGamedata->updateInventory($mineral, -$row['mineral_amount']);
                        $this->UpdateGamedata->updateInventory($wood, -$row['wood_amount'], true);
                        break;
                    case 'horse':
                        if($row_count != 0 ) {
                            // Update inventory
                            $this->UpdateGamedata->updateInventory('gold', (-$row['value'] + ($row2['value'] * 0.80)), true);
                        }
                        else {
                            // Update inventory
                            $this->UpdateGamedata->updateInventory('gold', -$row['value'], true);
                        }
                        break;
                }
                $param_item = $item;
                $param_city_amount = $newStoreAmount;
                //Update store
                $sql3 = $updateSTMT3;
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":city", $param_city_amount, PDO::PARAM_STR);
                $stmt3->bindParam($selectBind1, $param_item, PDO::PARAM_STR);
                $stmt3->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", $boughtMessage);
        }
    }
?>