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
            $sql = "SELECT item, amount, want, want_amount FROM merchants WHERE location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $stmt->execute();
            $data['shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
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
        public function buyItem($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from merchant.js
            // Function to trade item with merchant
            $item = strtolower($POST['item']);
            $amount = $POST['amount'];
            $bond = $POST['bond'];
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again later!", true);
                return false;
            }
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

            $total_amount = $amount * $row['want_amount'];
            //Check if item exists in users stockpile
            try {
                $this->db->conn->beginTransaction();
                if($bond == 'false') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($row['want'], -$total_amount); 
                }
                else {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory('trade bond', -1);  
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($item, $amount, true);
            
                //Update merchant
                $sql3 = "UPDATE merchants SET amount=:amount WHERE item=:item";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":amount", $param_newValue, PDO::PARAM_STR);
                $stmt3->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_newValue = $row['amount'] - $amount;
                $param_item = $item;
                $stmt3->execute();
            
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>