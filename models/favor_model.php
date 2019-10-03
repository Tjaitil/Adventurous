<?php
    class favor_model extends model {
        public $username;
        public $session;
        public $location;
        public $destination;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->location = $this->session['location'];
            $this->destination = $this->session[$this->base]['favor']['destination'];
        }
        
        public function newFavorDelivery($item, $amount) {
            $sql = "SELECT assignment_id, cart_amount FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['assignment_id'] != 0) {
                $this->gameMessage("ERROR: You already have a assignment");
                return false;
            }
            
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE trader_assigment SET base=:base, destination=:destination, cargo=:cargo, cargo_amount=:cargo_amount
                        WHERE assignment_id=1";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":base", $param_base, PDO::PARAM_STR);
                $stmt->bindParam(":destination", $param_destination, PDO::PARAM_STR);
                $stmt->bindParam(":cargo", $param_cargo, PDO::PARAM_STR);
                $stmt->bindParam(":cargo_amount", $param_cargo_amount, PDO::PARAM_STR);
                $param_base = $this->base;
                $param_destination = $this->destination;
                $param_cargo = $this->session[$destination]['favor']['item'];
                $param_cargo_amount = $this->session[$destination]['favor']['amount'];
                
                $sql2 = "UPDATE trader SET assignment_amount=:assignment_amount, assignment_id=:assignment_id, cart_amount=:cart_amount 
                        WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":assignment_amount", $param_assignment_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $stmt2->bindParam(":cart_amount", $param_cart_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_assignment_amount = $row2['capasity'];
                $param_assignment_id = 1;
                $param_cart_amount = $row2['capasity'];
                $param_username = $this->username;
                $stmt2->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
        
        public function pickUp() {
            $sql = "SELECT assignment_amount, assignment_id, cart_amount, cart FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(!$row['assignment_id'] > 0) {
                $this->gameMessage("ERROR: You don't have any assignment at the moment");
                return false;
            }
            
            $sql = "SELECT base FROM trader_assignments WHERE assignment_id=1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row2['base'] != $this->location) {
                $this->gameMessage("ERROR: You are in the wrong city to pick up items");
                return false;
            }
            
            $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
            $param_wheel = $row['cart'];
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);

            
            if($row['cart_amount'] == $row3['capasity']) {
                echo "Your cart is full";
                return false;
            }
            
            $cart_space = $row3['capasity'] - $row['cart_amount'];
            
            // If assignment_amount is less than the cart space available;
            if($row['assignment_amount'] < $cart_space) {
                $cart_space = $row['assignment_amount'];
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
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("You have picked up " . $cart_space . " items", true);   
        }
        
        
        public function deliver() {
            $sql = "SELECT assignment_id, cart_amount FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['assignment_id'] != 1) {
                $this->gameMessage("ERROR: You don't have a assignment");
                return false;
            }
            
            $sql = "SELECT destination, cargo_amount FROM trader_assignments WHERE assignment_id=1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row2['destination'] != $this->location) {
                $this->gameMessage("ERROR: You are in the wrong city to deliver");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE trader SET delivered=:delivered, cart_amount=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":delivered", $param_delivered, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_delivered = $row['cart_amount'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE trader_assignment SET base='none', destination='none', cargo='none', cargo_amount='none'
                        WHERE assignment_id=:1"; 
                $stmt = $this->db->conn->query($sql);
                
                $sql = "UPDATE diplomacy SET Hirtam=:Hirtam, Pvitul=:Pvitul, Khanz=:Khanz, Ter=:Ter, Fansal Plains=:Fansal Plains
                        WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":Hirtam", $param_Hirtam, PDO::PARAM_STR);
                $stmt->bindParam(":Pvitul", $param_Pvitul, PDO::PARAM_STR);
                $stmt->bindParam(":Khanz", $param_Khanz, PDO::PARAM_STR);
                $stmt->bindParam(":Ter", $param_Ter, PDO::PARAM_STR);
                $stmt->bindParam(":Fansal Plains", $param_FansalPlains, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_Hirtam = $new_diplomacy['Hirtam'];
                $param_Pvitul = $new_diplomacy['Pvitul'];
                $param_Khanz = $new_diplomacy['Khanz'];
                $param_Ter = $new_diplomacy['Ter'];
                $param_FansalPlains = $new_diplomacy['FansalPlains'];
                $param_username = $this->username;
                $stmt->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>