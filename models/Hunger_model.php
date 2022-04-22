<?php
    class Hunger_model extends model {
        public $username;
        public $session;
        public $hunger_db;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function checkHunger() {
            // This function is called from an ajax request
            // Function to calculate hunger by todays time and the last time they ate
            $sql = "SELECT hunger, hunger_date FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->hunger_db = $stmt->fetch(PDO::FETCH_ASSOC);
            
            /*
             * Divided by 60 you get the minutes
             * Divided by 60 you get hours
             */
            /*$hunger = $row['hunger'] - floor(($date_now - $hunger_date) / 60 / 60);
            if($hunger < 0) {
                $hunger = 0;
            }
            
                $sql = "UPDATE user_data SET hunger=:hunger WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":hunger", $param_hunger, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_hunger = $hunger;
                $param_username = $this->username;
                $stmt->execute();*/
            $_SESSION['gamedata']['hunger'] = $this->hunger_db['hunger'];
            echo $this->hunger_db['hunger'];
        }
        public function updateHunger($POST) {
            // $POST variable holds post data
            // This function is called from an ajax request
            // Function to calculate new hunger bar after pause has happened in client
            $seconds = $POST['time'];
            // If intval of seconds is 0 there has been tampering of the date object
            if(intval($seconds) === 0) {
                $seconds = 2400;
            }
            if(!isset($_SESSION['gamedata']['hunger'])) {
                $hunger = 0;
            }
            else {
                $hunger = $_SESSION['gamedata']['hunger'];
            }
            
            $minutes = round($seconds / 60);
            // For every minute reduce hunger by 2.5 points;
            $new_hunger =  $hunger - ($minutes * 2.5);
            if($new_hunger < 0) {
                $new_hunger = 0;
            }
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE user_data SET hunger=:hunger WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":hunger", $param_hunger, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_hunger = $new_hunger;
                $param_username = $this->username;
                $stmt->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            echo $new_hunger;
        }
        public function eat($POST) {
            // $POST variable holds post data
            // This function is called from an ajax request
            // Function to replenish players hunger bar by eating food
            $item = strtolower($POST['item']);
            $amount = $POST['amount'];
            
            $item_data = get_item($this->session['inventory'], $item);
            if(!count($item_data) > 0) {
                $this->gameMessage("ERROR: You don't currently have that item in your inventory", true);
                return false;
            }
            if(!$item_data['amount'] > 0) {
                $this->gameMessage("ERROR: You don't currently have that many in your inventory", true);
                return false;
            }
            
            $sql = "SELECT heal FROM bakery_data WHERE type=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: This item wouldn't taste good!", true);
                return false;
            }   
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT hunger, hunger_date, hunger FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Prevent player from using too many food units to replenish hunger bar
            /*if(($row['heal'] * $amount) + $row2['hunger'] > 100) {
                $count = 0;
                for($i = 0; $i < count($amount); $i++) {
                    if($row2['hunger'] + $row['heal'] < 100) {
                        $count++;
                    }
                    else {
                        return;
                    }
                }
                $amount = $count;
            }*/
            
            $hunger = ($row['heal'] * $amount) + $row2['hunger'];
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE user_data SET hunger=:hunger, hunger_date=:hunger_date WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":hunger", $param_hunger, PDO::PARAM_INT);
                $stmt->bindParam(":hunger_date", $param_hunge_date, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_hunger = $hunger;
                $param_hunger_date = date("Y-m-d H:i:s");
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($item, -$amount, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
        }
    }
?>