<?php
    class Hunger_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function checkHunger() {
            // This function is called from an ajax request
            // Function to calculate hunger by todays time and the last time they ate
            $sql = "SELECT hunger, hunger_date, hunger FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
            $hunger_date = date_timestamp_get(new DateTime($row['hunger_date']));
            
            $hunger = $row['hunger'] - floor(($date_now - $hunger_date) / 60 / 30);
            
            $_SESSION['gamedata']['hunger'] = $hunger;
            echo $hunger;
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
            if(!$stmt->rowCount() < 0) {
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
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
        }
    }
?>