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
            $item = strtolower($item);
            // Function to update the players inventory in database and also session
            $param_item = $item;
            $param_username = $this->username;
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";  
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            
            // If item does not exist in the database
            if(!$count > 0) $row['amount'] = 0;

            // If there is 18 inventory items throw error
            if($count == 0 && count($this->session['inventory']) == 18) {
                throw new Exception("ERROR: inv_amount", true);
            }
            
            if($count === 0 && $quantity > 0) {
                // Insert new item into bank
                $param_username = $this->username;
                $param_item = $item;
                $param_amount = $quantity;
                $sql = "INSERT INTO inventory (username, item, amount) VALUES(:username, :item, :amount)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt->execute();
            }
            $new_amount = $row['amount'] + $quantity;
            if($row['amount'] > 0 && $new_amount > 0) {
                // If items already exists in bank
                $param_amount = $new_amount;
                $param_username = $this->username;
                $param_item = $item;
                $sql2 = "UPDATE inventory SET amount=:amount WHERE username=:username AND item=:item";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
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
            
            $param_xp = $this->session[$profiency]['xp'] + $xp;
            $param_username = $this->username;
            $sql = "UPDATE user_levels SET {$profiency}_xp=:xp WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":xp", $param_xp, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $_SESSION['gamedata'][$profiency]['xp'] = $this->session[$profiency]['xp'] + $xp;
            // $conn is unset by the model that is instantiating this class
            
            // Check if the profiency has leveled up
            if($_SESSION['gamedata'][$profiency]['xp'] > $this->session[$profiency]['next_level']) {
                $_SESSION['gamedata']['level_up'][] = $profiency;
                $levelUp_model = $this->loadModel('LevelUp', true, true);
                return $levelUp_model->updateData();
            }
            else {
                return array();
            }
        }
        // public function updateDiplomacy() {
        //     $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
        //         if(in_array($this->session['location'], $locations)) {
        //             $new_diplomacy = array();
        //             for($i = 0; $i < count($diplomacy); $i++) {
        //                 // Change city relations if it is not 1. If it is 1 it will be the same anyways
        //                 // Calculate a increase or decrease by measuring b
        //                 // Example 1.50 -> $oldDiplomacy + ((1.50 - 1 ) - 0,6);
        //                 if($city_relations[$locations[$i]] !== 1) {
        //                     if($city_relations[$locations[$i]] > 1) {
        //                         $new_diplomacy[$locations[$i]] = 
        //                         floatval($diplomacy[$locations[$i]]) +
        //                             (($city_relations[$locations[$i]] - 1) - (($city_relations[$locations[$i]] -1) * 0.90));
        //                     }
        //                     else {
        //                         $new_diplomacy[$locations[$i]] = 
        //                         floatval($diplomacy[$locations[$i]]) -
        //                             ((1 - $city_relations[$locations[$i]]) - ((1 - $city_relations[$locations[$i]]) * 0.90));
        //                     }
        //                 }
        //             }
        //             $param_Hirtam = $new_diplomacy['hirtam'];
        //             $param_Pvitul = $new_diplomacy['pvitul'];
        //             $param_Khanz = $new_diplomacy['khanz'];
        //             $param_Ter = $new_diplomacy['ter'];
        //             $param_FansalPlains = $new_diplomacy['fansalplains'];
        //             $param_username = $this->username;
        //             $sql2 = "UPDATE diplomacy 
        //                     SET hirtam=:hirtam, pvitul=:pvitul, khanz=:khanz, ter=:ter, fansalplains=:fansalplains
        //                     WHERE username=:username";
        //             $stmt2 = $this->db->conn->prepare($sql2);
        //             $stmt2->bindParam(":hirtam", $param_Hirtam, PDO::PARAM_STR);
        //             $stmt2->bindParam(":pvitul", $param_Pvitul, PDO::PARAM_STR);
        //             $stmt2->bindParam(":khanz", $param_Khanz, PDO::PARAM_STR);
        //             $stmt2->bindParam(":ter", $param_Ter, PDO::PARAM_STR);
        //             $stmt2->bindParam(":fansalplains", $param_FansalPlains, PDO::PARAM_STR);
        //             $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
        //             $stmt2->execute();
        //             $this->response->addTo("gameMessage", "Diplomacy relations have been updated! See diplomacy tab");
        //         }
        // }
    }
?>