<?php
    class Profiency_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function getData() {
            //Get profiency
            $param_username = $this->username;
            $sql = "SELECT profiency FROM user_data WHERE username=:username";          
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            return $row['profiency'];
        }
        
        public function changeProfiency($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from citycentre.js
            // Function to change the profiency of the player
            $new_profiency = strtolower($POST['newProfiency']);
            if($this->session['gold'] < 500) {
                $this->response->addTo("errorGameMessage", "You dont have enough gold!");
                return false;
            }
            
            if($this->session['profiency_level'] > 30) {
                
            }
                        
            try {
                $this->db->conn->beginTransaction();
                
                if($this->session['profiency_level'] > 30) {
                    $param_username = $this->username;
                    $sql = "UPDATE " . $this->session['profiency'] . "=30 WHERE username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt->execute();
                }
                $param_profiency = $new_profiency;
                $param_username = $this->username;
                $sql = "UPDATE user_data SET profiency=:profiency WHERE username=:username";             
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -500, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->response->addTo("gameMessage", "You have changed your profiency to {$new_profiency}");
            $_SESSION['gamedata']['profiency'] = $new_profiency;
            $this->db->closeConn();
        }
    }
?>