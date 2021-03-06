<?php
    class Workers_model extends model {
        public $username;
        public $session;
        
        function __construct ($session, $db) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function upgradeEffiency($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from citycentre.js
            // Function to upgrade efficiency level for farmer and miner
            $skill = $POST['skill'];
            if(in_array($skill, array('farmer', 'miner')) != true) {
                return false;
            }
            
            $sql = "SELECT efficiency_level FROM {$skill}_workforce WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT max_efficiency_level FROM level_data WHERE level=:level";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
            $param_level = $this->session[$skill]['level'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
                        
            if($row2['max_efficiency_level'] == $row['efficiency_level']) {
                $this->gameMessage("ERROR: You have reached the maximum efficiency level for your skill level", true);
                return false;
            }
            
            $cost = $row['efficiency_level'] * 150;
            
            if($this->session['gold'] > $cost) {
                $this->gameMessage("ERROR: You don't have enough gold to upgrade!", true);
                return false;
            }
            try {
                $this->db->conn->beginTransacion();
                
                $sql = "UPDATE {$skill}_workforce SET efficiency_level=:efficiency_level WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":efficiency_level", $param_efficiency_level, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_efficiency_level = $row['effency_level'] + 1;
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -$amount, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->gameMessage("Your efficiency level for {$skill} workforce is now {$param_efficiency_level}", true);
            echo "|" . $param_efficiency_level;
        }
    }
?>