<?php
    class trader_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function pickUp($favor = false) {
            //AJAX function
            $sql = "SELECT assignment_amount, assignment_id, cart_amount, cart FROM trader WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$row['assignment_id'] > 0) {
                $this->gameMessage("ERROR: You don't have any assignment at the moment", true);
                return false;
            }
            
            $sql = "SELECT base FROM trader_assignments WHERE assignment_id=:assignment_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
            $param_assignment_id = $row['assignment_id'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row2['base'] != $this->session['location']) {
                $this->gameMessage("ERROR: You are in the wrong city to pick up items", true);
                return false;
            }
            
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);

            
            if($row['cart_amount'] == $row3['capasity']) {
                $this->gameMessage("ERROR: Your cart is full", true);
                return false;
            }
            $cart_space = $row3['capasity'] - $row['cart_amount'];
            // If assignment_amount is less than the cart space available;
            if($row['assignment_amount'] < $cart_space) {
                $cart_space = $row['assignment_amount'];
            }
        
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE trader SET cart_amount=:cart_amount WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":cart_amount", $param_cart_amount, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_cart_amount = $row['cart_amount'] + $cart_space;
                $param_username = $this->username;
                $stmt->execute();
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $this->gameMessage("You have picked up " . $cart_space . " items", true);   
        }
        
        public function deliver($favor = false) {
            $sql = "SELECT assignment_amount, assignment_id, cart_amount, delivered FROM trader
                    WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['assignment_amount'] == 0) {
                $this->gameMessage("ERROR: You don't have a any assignment", true);
                return false;
            }
            
            if(!$row['cart_amount'] > 0) {
                $this->gameMessage("ERROR: You dont't have any goods to deliver", true);
                return false;
            }
            
            $sql = "SELECT assignment_type, destination FROM trader_assignments WHERE assignment_id=:assignment_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
            $param_assignment_id = $row['assignment_id'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row2['destination'] != $this->session['location']) {
                $this->gameMessage("ERROR: You are in the wrong city to deliver", true);
                return false;
            }
            if($favor != true) {
                $sql = "SELECT xp FROM assignment_types WHERE type=:type";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $param_type = $row2['assignment_type'];
                $param_assignment_id = $row['assignment_id'];
                $stmt->execute();
                $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                $xp = $row3['xp'] * $row['cart_amount'];
            }
            else {
                $xp = 0;
            }
            $delivered = $row['delivered'] + $row['cart_amount']; 
            
            try {
                $this->conn->beginTransaction();
                $sql =  "UPDATE trader SET delivered=:delivered, cart_amount=0 WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":delivered", $param_delivered, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_delivered = $delivered;
                $param_username = $this->username;
                $stmt->execute();
                
                if($favor != true) {
                    $sql = "UPDATE user_levels SET trader_xp=:trader_xp WHERE username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_trader_xp = $xp + $this->session['trader']['xp'];
                    $param_username = $this->username;
                    $stmt->execute();
                }
                
                $this->conn->commit();
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $this->gameMessage(($row['assignment_amount'] == $delivered)? "Assignment completed" : " ", true);
            $_SESSION['gamedata']['trader']['xp'] = $param_trader_xp;
            //Echo to prevent getting the timestamp from gameMessage()
            echo "You have delivered:" . $row['cart_amount'] ."</br>" . "Total:" . $param_delivered;
        }
    }
?>