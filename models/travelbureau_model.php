<?php
    class travelbureau_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData($js = false) {
            $city = $_SESSION['gamedata']['location'];
            if($_SESSION['gamedata']['location'] === 'travelling') {
                header("Location: /city");
                exit();
            }
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                return false;
            }
            $sql = "SELECT type, $city, value FROM travelbureau_horses";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_amount = 0;
            $stmt->execute();
            $data = array();
            $data['horse_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data['city'] = $city;
            
            $sql = "SELECT wheel, wood, value, capasity, $city, mineral_amount, wood_amount FROM travelbureau_carts"; 
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $data['cart_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
             // if statement to check if ajax request is being called
            if($js === true) {
                // "||" is for next item and "#" is to split the different shops
                /*foreach($data['horseShop'] as $key) {
                    echo $key['type'] .'|'. $key['value'] .'|'. $key[$data['city']] . '||';
                }
                echo "#";
                foreach($data['cartShop'] as $key) {
                    echo $key['wheel'] . '|' . $key['wood']. '|' . $key['value'] . '|' . $key['capasity'] . '|' . $key[$data['city']] . '||';
                }*/
                get_template('horseShop', $data, true);
                echo "#";
                get_template('cartShop', $data, true);
            }
            else {
                return $data;
            }
        }
        
        public function buyItem($shop, $item) {
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
                    $boughtMessage = "You bough a $item cart for ";
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
                    $boughtMessage = "You bough a $item horse for ";
                break;
             }
            $sql = $selectSTMT1;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam($selectBind1, $param, PDO::PARAM_STR);
            $param = $item;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            if(!$count > 0) {
                $this->gameMessage("ERROR: The item you are trying to buy does not exists!", true);
                return false;
            }
            switch($shop) {
                case 'cart':
                    $mineral = $row['wheel'] . ' ore';
                    $mineral_amount = get_item($this->session['inventory'], $mineral)['amount'];
                    if($mineral_amount < $row['mineral_amount']) {
                        $this->gameMessage("ERROR: You don't have enough $mineral to buy this!", true);
                        return false;
                    }
                    $wood = $row['wood'] . ' log';
                    $wood_amount = get_item($this->session['inventory'], $wood)['amount'];
                    if($wood_amount < $row['wood_amount']) {
                        $this->gameMessage("ERROR: You don't have enough " . $wood . " to buy this!", true);
                        return false;
                    }
                    break;
                case 'horse':
                    if($row['value'] > $this->session['gold'] && $shop == 'horse') {
                        $this->gameMessage("ERROR: You don't have enough gold to buy this item!", true);
                        return false;
                    }
                    break;
            }
        
            if(!$row[$city] > 0 ) {
                $this->gameMessage("ERROR: There are none left of this item in stock", true);
                return false;
            }
            //Check if user has a horse already, if so 80% of the price will be refunded.
            $sql = $selectSTMT2;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row_count = $stmt->rowCount();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $newStoreAmount = $row[$city] - 1;
            try {
                $this->conn->beginTransaction();
                
                //Update cart to trader/ horse to user
                $sql = $updateSTMT1;
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam($updateBind1, $param, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param = $item;
                $param_username = $this->username;
                $stmt->execute();
                
                //Update gold amount, if $row_count is greater than 0 the user has either cart/horse which is then sold
                switch($shop) {
                    case 'cart':
                        if($row_count != 0) {
                            update_inventory($this->conn, $this->username, 'gold', ($row2['value'] * 0.80), true);
                        }
                        update_inventory($this->conn, $this->username, $mineral, -$row['mineral_amount']);
                        update_inventory($this->conn, $this->username, $wood, -$row['wood_amount']);
                        break;
                    case 'horse':
                        if($row_count != 0 ) {
                            update_inventory($this->conn, $this->username, 'gold', (-$row['value'] + ($row2['value'] * 0.80)), true);
                        }
                        else {
                            update_inventory($this->conn, $this->username, 'gold', -$row['value'], true); 
                        }
                        break;
                }
                //Update store
                $sql3 = $updateSTMT3;
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":city", $param_city_amount, PDO::PARAM_STR);
                $stmt3->bindParam($selectBind1, $param_item, PDO::PARAM_STR);
                $param_item = $item;
                $param_city_amount = $newStoreAmount;
                $stmt3->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $this->gameMessage($boughtMessage, true);
        }
    }
?>