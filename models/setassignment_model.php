<?php
    class setassignment_model extends model {
        public $username;
        public $session;
        public $destination;
        public $favor;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
            $this->destination = $this->session['favor']['destination'];
            $this->favor = $this->session['favor'];
        }
        
        public function newAssignment($assignment_id, $favor = false) {
            /*$assignment_amount = str_replace(" ", "+", $assignment_amount);*/
            $sql = "SELECT assignment_id, cart FROM trader WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['assignment_id'] > 0) {
                $this->gameMessage("ERROR! Finish your assignment before taking a new one", true);
                return false;
            }
            if($row['cart'] == 'none') {
                $this->gameMsssage("ERROR! You don't have a cart", true);
                return false;
            }
            if($favor != true) {
                $sql = "SELECT destination, cargo_amount, time, assignment_type FROM trader_assignments WHERE assignment_id=:assignment_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $assignment_id;
                $stmt->execute();
                $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $assignment_id = 1;
                $assignment_data['cargo_amount']  = $this->session['favor']['amount'];
            }
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cart_capasity = $row['capasity'];
            
            if($favor != true) {
                $sql2 = "SELECT xp, time FROM assignment_types WHERE type=:type";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":type", $param_type, PDO::PARAM_STR);
                $param_type = $assignment_data['assignment_type'];
                $stmt2->execute();
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                $assignment_XP = $row2['xp'];

            //countdown;
            $add_time = $assignment_data['time'];
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$add_time} seconds");
            }
            else {
                $assignment_XP = 0;
                $date = date("Y-m-d H:i:s");
                $new_date = new DateTime($date);
            }
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE trader SET trader_xp=:trader_xp, assignment_amount=:assignment_amount, assignment_id=:assignment_id,
                        cart_amount=:cart_amount, trading_countdown=:trading_countdown WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                $stmt->bindParam(":assignment_amount", $param_assignment_amount, PDO::PARAM_STR);
                $stmt->bindParam(":assignment_id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":cart_amount", $param_cart_amount, PDO::PARAM_STR);
                $stmt->bindParam(":trading_countdown", $param_trading_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_trader_xp = $this->session['trader']['xp'] + $assignment_XP;
                $param_assignment_amount = $assignment_data['cargo_amount'];
                $param_id = $assignment_id;
                $param_cart_amount = $cart_capasity;
                $param_trading_countdown = date_format($new_date, "Y-m-d H:i:s");
                $param_username = $this->username;
                $stmt->execute();
                
                if($favor != true) {
                    $sql2 = "UPDATE user_levels SET trader_xp=:trader_xp WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    //trader_xp already defined in statement 1
                    $param_username = $this->username;
                    $stmt2->execute();
                }
                else {
                    $sql = "UPDATE trader_assignments SET base=:base, destination=:destination, cargo=:cargo, cargo_amount=:cargo_amount
                        WHERE assignment_id=1";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":base", $param_base, PDO::PARAM_STR);
                    $stmt->bindParam(":destination", $param_destination, PDO::PARAM_STR);
                    $stmt->bindParam(":cargo", $param_cargo, PDO::PARAM_STR);
                    $stmt->bindParam(":cargo_amount", $param_cargo_amount, PDO::PARAM_STR);
                    $param_base = $this->session['location'];
                    $param_destination = $this->destination;
                    $param_cargo = $this->favor['item'];
                    $param_cargo_amount = $this->favor['amount'];
                    $stmt->execute();
                }
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            if ($favor != true) {
                $_SESSION['gamedata']['trader']['xp'] = $param_trader_xp;
            }
            $this->closeConn();
            $this->gameMessage("New assignment taken", true);
        }
    }
?>