<?php
    public function newAssignment($trader_xp, $profiency, $assignment_id) {
            if($profiency != 'trader') {
                echo "Your current profiency is not trader";
                return false;
            }
            /*$assignment_amount = str_replace(" ", "+", $assignment_amount);*/
            $sql = "SELECT assignment_id, cart FROM trader WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['assignment_id'] === 0) {
                echo "Finish your assignment before taking a new one";
                return false;
            }
            if($row['cart'] == 'none') {
                echo "You don't have a cart";
                return false;
            }
            if(!isset($favor)) {
                $sql = "SELECT destination, cargo_amount, time, assignment_type FROM trader_assignments WHERE assignment_id=:assignment_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $assignment_id;
                $stmt->execute();
                $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $assignment_id = 1;
                $assignment_data['cargo_amount']  = $this->session[$location]['favor']['amount'];
            }
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cart_capasity = $row['capasity'];
            
            if(!isset($favor) {
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
                $assignment_xp = 0;
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
                $param_trader_xp = $trader_xp + $assignment_XP;
                $param_assignment_amount = $assignment_data['cargo_amount'];
                $param_id = $assignment_id;
                $param_cart_amount = $cart_capasity;
                $param_trading_countdown = date_format($new_date, "Y-m-d H:i:s");
                $param_username = $this->username;
                $stmt->execute();
                if(!isset($favor)) {
                    $sql2 = "UPDATE user_levels SET trader_xp=:trader_xp WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    //trader_xp already defined in statement 1
                    $param_username = $this->username;
                    $stmt2->execute();
                }
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollback();
                print "ERROR" . $e->getMessage();
            }
            if (!isset($favor)) {
                $_SESSION['gamedata']['trader']['xp'] = $param_trader_xp;
            }
            echo "New assignment taken";
            unset($stmt, $stmt2);
            unset($this->conn);
        }
?>