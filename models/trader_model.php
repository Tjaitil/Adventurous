<?php
    class trader_model extends model {
        public $username;
        public $session;
        public $assignment_types;
        public $assignment_type;
        public $assignment_amount;
        public $cargo;
        public $favor;

        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
            $this->assignment_types = $assignment_types = restore_file('trader_assignment_types', true);
        }
        public function pickUp() {
            //AJAX function
            $sql = "SELECT t.assignment_id, t.cart, t.cart_amount, t.delivered, ta.assignment_amount, ta.assignment_type, ta.base
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username"; 
            $stmt = $this->conn->prepare($sql);
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
            $stmt = $this->conn->prepare($sql);
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
            echo "|{$param_cart_amount}/{$row3['capasity']}";
        }   
        public function deliver() {
            //AJAX function
            $sql = "SELECT t.assignment_id, t.cart_amount, t.delivered, ta.assignment_amount, ta.cargo, ta.assignment_type, ta.destination
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username"; 
            $stmt = $this->conn->prepare($sql);
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
            
            $xp = $assignment_type[0]['xp'] * $row['cart_amount'];
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
                
                // Only gain xp when miner level is below 30 or if profiency is miner
                if($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader') {
                    update_xp($this->conn, $this->username, 'trader', $xp + $this->session['trader']['xp']);
                } 
                
                $this->conn->commit();
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            //Echo to prevent getting the timestamp from gameMessage()
            $this->gameMessage("You have delivered: {$row['cart_amount']}, Total: {$param_delivered}", true);
            echo "|";
            jsecho(array($xp, $row['cart_amount'],"{$delivered}/{$row['assignment_amount']}"));
            if($row['assignment_amount'] == $delivered) {
                $this->assignment_type = $row['assignment_type'];
                $this->assignment_amount = $row['assignment_amount'];
                $this->cargo = $row['cargo'];
                $this->updateAssignment();
            }
            else {
                $this->closeConn();
            }
        }
        private function updateAssignment() {
            if($this->favor != true) {
                $sql = "SELECT cart_amount, trading_countdown FROM trader WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $trading_countdown = new DateTime($row['trading_countdown']);
            }
            else {
                $date = date("Y-m-d H:i:s");
                $trading_countdown = new DateTime($date);
            }
            
            $datetime = date_create();
            $date_now = date_timestamp_get($datetime);
            $date_assignment = date_timestamp_get($trading_countdown);
            
            switch($this->assignment_type) {
                case 'small trade':
                    $xp = 50;
                    break;
                case 'medium trade':
                    $xp = 100;
                    break;
                case 'favor':
                    $xp = 90;
                    break;
                case 'long trade':
                    $xp = 150;
                    break;
            }
            
            if($date_assignment > $date_now && $this->favor == false) {
                $time_left = $date_assignment - $date_now;
                // Calculate the time that is left, 0.2xp for every minute
                $xp += $xp_bonus = round($time_left * 0.1);
            }
            if($this->favor === true) {
                $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $diplomacy = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM city_relations WHERE city=:city";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
                $param_city = $this->session['favor']['base'];
                $stmt->execute();
                $city_relations = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $new_diplomacy = array();
                for($i = 0; $i < count($diplomacy); $i++) {
                    $new_diplomacy[$locations[$i]] = intval($diplomacy[$locations[$i]]) * $city_relations[$locations[$i]];
                }
            }
            $reward_amount =  round($this->assignment_amount / 5);
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE trader SET assignment_id=0, delivered=0
                        WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_trader_xp = $xp + $this->session['trader']['xp'];
                $param_username = $this->username;
                $stmt->execute();
                
                // Only gain xp when miner level is below 30 or if profiency is miner
                if($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader') {
                    update_xp($this->conn, $this->username, 'trader', $xp + $this->session['trader']['xp']);
                }
                
                if($this->favor != true) {
                    update_inventory($this->conn, $this->username, $this->cargo , $reward_amount, true);
                }
                else {
                    $sql2 = "UPDATE diplomacy SET hirtam=:hirtam, pvitul=:pvitul, khanz=:khanz, ter=:ter, fansalplains=:fansalplains
                        WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
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
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            
            $this->closeConn();
            echo "|finished!|";
            if($this->favor != true) {
                if(isset($xp_bonus)) {
                    $this->gameMessage("You received {$xp_bonus} for finishing before deadline!", true);
                    echo "|";
                }
                $this->gameMessage("You finished assignment and received {$reward_amount} of {$this->cargo}", true);
                echo "|{$xp}";
            }
            else {
                $this->gameMessage("You have finsihed your favor assignment", true);
                unset($_SESSION['gamedata']['favor']);
                echo "|{$xp}";
            }
        }
    }
?>