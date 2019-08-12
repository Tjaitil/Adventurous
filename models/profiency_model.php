<?php
    class profiency_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData() {
            //Get profiency
            $sql = "SELECT profiency FROM user_data WHERE username=:username";          
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $row['profiency'];
        }
        
        public function changeProfiency($newProfiency) {
            $gold = $this->session['gold'];
            $newProfiency = strtolower($newProfiency);
            if($gold < 500) {
                $this->gameMessage("ERROR: You dont have enough gold!", true);
                return false;
            }
            try {
                $this->conn->beginTransaction();
                
                if($this->session['profiency_level'] > 30) {
                    // FIKS
                }
                $sql = "UPDATE user_data SET profiency=:profiency WHERE username=:username";             
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_profiency = $newProfiency;
                $param_username = $this->username;
                $stmt->execute();
                
                update_inventory($this->conn, $this->username, 'gold', -500, true);
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("You have succesfully changed profiency to {$newProfiency}", true);
            $_SESSION['gamedata']['profiency'] = $newProfiency;
            unset($this->conn);
        }
    }
?>