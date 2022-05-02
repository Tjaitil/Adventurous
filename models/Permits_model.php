<?php
    class Permits extends model {
        // Class is a grouping of functions which can be called from ajax request from /citycentre
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function buyPermits($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from citycentre.js
            // Function to buy mine permits for user
            $permit_amount = $POST['amount'];
            $selected_location = $POST['selectedLocation'];
            if($this->session['gold'] < 50) {
                $this->response->addTo("errorGameMessage", "You don't have enough gold!");
                return false;
            }
            if(!strlen($selected_location) > 1) {
                $this->response->addTo("errorGameMessage", "Please select a location");
                return false;
            }
            
            $param_username = $this->username;
            $param_location = $selected_location;
            $sql = "SELECT permits FROM miner WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                $param_permits = intval($row2['permits']) + $permit_amount;
                $param_username = $this->username;
                $param_location = $selected_location;
                $sql = "UPDATE miner SET permits=:permits WHERE username=:username AND location=:location";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -50, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "You bought 50 permits in {$selected_location}  50 gold");
            $this->response->addTo("data", $param_permits, array("index" => "permits"));
        }
    }
?>