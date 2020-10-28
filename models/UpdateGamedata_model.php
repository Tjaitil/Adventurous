<?php
    class UpdateGamedata_model extends model {
        public $username;
        public $session;
        protected $db;
        
        function __construct ($session, $db) {
            $this->db = $db;
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function updateInventory($item, $quantity, $update = false, $unset = false) {
            // Function to update the players inventory in database and also session
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";  
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            
            if($count === 18 && $count === 0) {
                throw new Exception("This is an error! inv_amount");
            }
            
            if($count === 0 && $quantity > 0) {
                // Insert new item into bank
                $sql = "INSERT INTO inventory (username, item, amount) VALUES(:username, :item, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $param_username = $this->username;
                $param_item = $item;
                $param_amount = $quantity;
                $stmt->execute();
            }
            $new_amount = $row['amount'] + $quantity;
            if($count > 0 && $new_amount > 0) {
                // If items already exists in bank
                $sql2 = "UPDATE inventory SET amount=:amount WHERE username=:username AND item=:item";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_amount = $new_amount;
                $param_username = $this->username;
                $param_item = $item;
                $stmt2->execute();
            }
            else if(!$new_amount > 0) {
                //If item is zero
                $sql = "DELETE FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $this->username;
                $stmt->execute();
            }
            //If multiple update_inventory are being made the update only happens at the last one
            if($update = true) {
                $sql = "SELECT item, amount FROM inventory WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $_SESSION['gamedata']['inventory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            if($item === 'gold') {
                $_SESSION['gamedata']['gold'] = get_item($_SESSION['gamedata']['inventory'], 'gold')['amount'];
            }
            // $conn is unset by the model that is instantiating this class
        }
        public function updateXP($profiency, $xp) {
            // Update the players experience in on of the profiences in database
            
            if($this->session[$profiency]['level'] >= 30 && $this->session['profiency'] != $profiency) {
                return false;    
            }
            $profiencies = array("adventurer", "farmer", "miner", "trader", "warrior");
            if(in_array($profiency, $profiencies) == false) {
                return false;
            }
            
            $sql = "UPDATE user_levels SET {$profiency}_xp=:xp WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":xp", $param_xp, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_xp = $this->session[$profiency]['xp'] + $xp;
            $param_username = $this->username;
            $stmt->execute();
            $_SESSION['gamedata'][$profiency]['xp'] = $this->session[$profiency]['xp'] + $xp;
            // $conn is unset by the model that is instantiating this class
            
            // Check if the profiency has leveled up
            if($this->session[$profiency]['xp'] > $this->session[$profiency]['next_level']) {
                $_SESSION['gamedata']['level_up'][] = $profiency;
                $levelUp_model = $this->loadModel('LevelUp', true);
                $levelUp_model->updateData();
            }
        }
    }
?>