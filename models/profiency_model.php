<?php
    class profiency_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            //Get profiency
            $sql = "SELECT profiency FROM user_data WHERE username=:username";          
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
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
                $this->gameMessage("ERROR: You dont have enough gold!", true);
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                
                if($this->session['profiency_level'] > 30) {
                    // FIKS
                }
                $sql = "UPDATE user_data SET profiency=:profiency WHERE username=:username";             
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_profiency = $new_profiency;
                $param_username = $this->username;
                $stmt->execute();
                
                update_inventory($this->db->conn, $this->username, 'gold', -500, true);
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("You have succesfully changed profiency to {$new_profiency}", true);
            $_SESSION['gamedata']['profiency'] = $new_profiency;
            $this->db->closeConn();
        }
    }
?>