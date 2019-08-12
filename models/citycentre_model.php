<?php
    class citycentre_model extends model  {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData() {
            $data = array();
            
            $sql = "SELECT artefact, uses_left FROM keep WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['artefact_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT permits FROM miner WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined in statement 1
            $stmt->execute();
            $data['permits'] = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $data;
        }
        
        
        public function updateProfiency() { //AJAX function
            if($_SESSION['gamedata']['profiency_level'] > 30) {
                // Update og remove stats som er over level 10;
            }
        }
        
        public function changeArtefact($artefact) { //AJAX function
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $artefact;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0 || $row['amount'] == 0) {
                $this->gameMessage("ERROR: You don't have that artefact in your inventory", true);
                return false;
            }
            
            $sql = "SELECT artefact, uses FROM keep WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            //Lagre uses et sted, men hvor? Inventory med en ekstra kolonne med special? 
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE keep SET artefact=:artefact WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                if($row2['uses'] > 0) {
                    update_inventory($this->conn, $this->username, "damaged " . $row2['artefact'] . '(' . $row2['uses'] . ')', 1);
                }
                
                update_inventory($this->conn, $this->username, $artefact, -1, true);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            js_echo(array($artefact, $amount));
        }
        
        public function buyPermits($permit_amount) {
            //AJAX function
            if($this->session['gold'] < 50) {
                $this->gameMessage("ERROR: You don't have enough gold!", true);
                return false;
            }
            
            $sql = "SELECT permits FROM miner WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE miner SET permits=:permits WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":permits", $param_permits, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_permits = $row2['permits'] + $permit_amount;
                $param_username = $this->username;
                $stmt->execute();
                
                update_inventory($this->conn, $this->username, 'gold', -50, true);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $this->gameMessage("You bought 50 permits for the price of 100 gold", true);
        }
    }
?>