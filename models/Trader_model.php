<?php
    class Trader_model extends model {
        public $username;
        public $session;
        public $assignment_types;
        private $assignment_type;
        private $assignment_amount;
        private $cargo;
        private $favor;

        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->assignment_types = $assignment_types = restore_file('trader_assignment_types', true);
            $this->commonModels(true, false);
        }
        public function pickUp() {
            //AJAX function
            if($this->session['hunger'] < 10) {
                $this->gameMessage("ERROR: Your hunger is too high, please eat!", true);
                return false;
            }
            
            $sql = "SELECT t.assignment_id, t.cart, t.cart_amount, t.delivered, ta.assignment_amount, ta.assignment_type, ta.base
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username"; 
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $favor = ($row['assignment_type'] === 'favor') ? true : false;
            
            if(!$row['assignment_id'] > 0) {
                $this->gameMessage("ERROR: You don't have any assignment at the moment", true);
                return false;
            }
            if($row['base'] != $this->session['location']) {
                $this->gameMessage("ERROR: You are in the wrong city to pick up items", true);
                return false;
            }
            
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['cart_amount'] == $row3['capasity']) {
                $this->gameMessage("ERROR: Your cart is full", true);
                return false;
            }
            if($row['assignment_amount'] - $row['cart_amount'] <= 0) {
                $this->gameMessage("ERROR: You don't need to pick up more items", true);
                return false;
            }
            $cart_space = $row3['capasity'] - $row['cart_amount'];
            // If assignment_amount is less than the cart space available;
            if($row['assignment_amount'] < $cart_space + $row['delivered']) {
                $cart_space = $row['assignment_amount'] - $row['delivered'];
            }
        
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE trader SET cart_amount=:cart_amount WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":cart_amount", $param_cart_amount, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_cart_amount = $row['cart_amount'] + $cart_space;
                $param_username = $this->username;
                $stmt->execute();
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            /* Echo order, split by "|"
             * [0] -> gameMessage
             * [1] -> $echo_data with updated game data
             */
            $echo_data = array();
            $this->gameMessage("You have picked up " . $cart_space . " items", true);
            $echo_data['delivered'] = $param_cart_amount;
            echo "|";
            echo json_encode($echo_data);
        }   
        public function deliver() {
            //AJAX function
            if($this->session['hunger'] < 10) {
                $this->gameMessage("ERROR: Your hunger is too high, please eat!", true);
                return false;
            }
            
            $sql = "SELECT t.assignment_id, t.cart_amount, t.delivered, ta.assignment_amount, ta.cargo, ta.assignment_type,
                            ta.destination, ta.base
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username"; 
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->favor = ($row['assignment_type'] === 'favor') ? true : false;
            
            if($row['assignment_id'] == 0) {
                $this->gameMessage("ERROR: You don't have a any assignment", true);
                return false;
            }
            if(!$row['cart_amount'] > 0) {
                $this->gameMessage("ERROR: You dont't have any goods to deliver", true);
                return false;
            }
            if($row['destination'] != $this->session['location']) {
                $this->gameMessage("ERROR: You are in the wrong city to deliver", true);
                return false;
            }
            $type = $row['assignment_type'];
            $assignment_type = array_values(array_filter($this->assignment_types, function ($key) use ($type) {
                   return ($key['type'] == $type);  
                }));
            
            $experience = $assignment_type[0]['xp'] * $row['cart_amount'];
            $delivered = $row['delivered'] + $row['cart_amount'];
            $assignment_finished = false;
            
            if($row['assignment_amount'] == $delivered) {
                $assignment_finished = true;
                $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $diplomacy = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM city_relations WHERE city=:city";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
                $param_city = $row['base'];
                $stmt->execute();
                $city_relations = $stmt->fetch(PDO::FETCH_ASSOC);
                $new_diplomacy = array();
                if($assignment_type === "favor") {
                    for($i = 0; $i < count($diplomacy); $i++) {
                        $new_diplomacy[$locations[$i]] = intval($diplomacy[$locations[$i]]) * $city_relations[$locations[$i]];
                    }
                }
            }
            switch($this->assignment_type) {
                case 'small trade':
                    $experience += 50;
                    break;
                case 'medium trade':
                    $experience += 100;
                    break;
                case 'favor':
                    $experience += 90;
                    break;
                case 'long trade':
                    $experience += 250;
                    break;
            }
            try {
                $this->db->conn->beginTransaction();
                if($assignment_finished === true) {
                    $sql = "UPDATE trader SET assignment_id=0, delivered=0, cart_amount=0
                        WHERE username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    if($assignment_type === "favor") {
                        $sql2 = "UPDATE diplomacy SET hirtam=:hirtam, pvitul=:pvitul, khanz=:khanz, ter=:ter, fansalplains=:fansalplains
                        WHERE username=:username";
                        $stmt2 = $this->db->conn->prepare($sql2);
                        $stmt2->bindParam(":hirtam", $param_Hirtam, PDO::PARAM_STR);
                        $stmt2->bindParam(":pvitul", $param_Pvitul, PDO::PARAM_STR);
                        $stmt2->bindParam(":khanz", $param_Khanz, PDO::PARAM_STR);
                        $stmt2->bindParam(":ter", $param_Ter, PDO::PARAM_STR);
                        $stmt2->bindParam(":fansalplains", $param_FansalPlains, PDO::PARAM_STR);
                        $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                        $param_Hirtam = $new_diplomacy['hirtam'];
                        $param_Pvitul = $new_diplomacy['pvitul'];
                        $param_Khanz = $new_diplomacy['khanz'];
                        $param_Ter = $new_diplomacy['ter'];
                        $param_FansalPlains = $new_diplomacy['fansalplains'];
                        $param_username = $this->username;
                        $stmt2->execute();
                    }
                    else {
                        $reward_amount =  round($row['assignment_amount'] / 12);
                        // If profiency is trader get goods
                        if($this->session['profiency'] == "trader") {
                            // Update inventory
                            $this->UpdateGamedata->updateInventory($row['cargo'] , $reward_amount, true);    
                        }
                        else {
                            // Update inventory
                            $this->UpdateGamedata->updateInventory($row['cargo'] , $reward_amount, true);            
                        }
                    }
                }
                else {
                    $sql = "UPDATE trader SET delivered=:delivered, cart_amount=0 WHERE username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":delivered", $param_delivered, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_delivered = $delivered;
                    $param_username = $this->username;
                    $stmt->execute();
                    $param_delivered = $delivered;
                }
                // Only gain xp when warrior level is below 30 or if profiency is trader and assignment_xp is greater than 0
                if($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader') {
                    $this->UpdateGamedata->updateXP('trader', $experience);
                    $xpUpdate = true;
                }
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            /* Echo order, split by "|"
             * [0] -> possible level up message;
             * [1] -> assignment finished / gameMessage delivered
             * [2] -> gameMessage if delivered, or new deliverd number
             * [3] -> traderAssignment get_template if assignment is completed / delivered amount
             */
            //Echo to prevent getting the timestamp from gameMessage()
            echo "|";
            $echo_data = array();
            if(isset($xpUpdate)) {
                if($row['assignment_amount'] == $delivered) {
                    $echo_data['assignment_finished'] = true;
                    $this->gameMessage("You finished assignment and received {$reward_amount} of {$this->cargo}", true);
                    echo "|";
                }
                else {
                    $echo_data['delivered'] = $delivered;
                    $echo_data['assignment_finished'] = false;
                    $this->gameMessage("You have delivered: {$row['cart_amount']}, Total: {$param_delivered}. Gained
                                        {$experience} trader experience", true);
                    echo "|";
                }
            }
            echo json_encode($echo_data);
            if($assignment_finished === true) {
                echo "|";
                $this->gameMessage("Diplomacy relations have been updated! See diplomacy tab", true);
                echo "|";
                get_template('traderAssignment', array("assignment_id" => 0), true);
            }
        }
    }
?>