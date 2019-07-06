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
            $profiency = $this->session['profiency_level'];
            $gold = $this->session['gold']['amount'];
            $newProfiency = strtolower($newProfiency);
            if($gold < 500) {
                echo "ERROR! You dont have enough gold!";
                return false;
            }
            else {
                $newBalance = $gold - 500;
            }
            if($profiency > 30) {
                // Update all profiency places where info is over level 30;                
            }
            else if ($profiency <= 30) {
                try {
                    $this->conn->beginTransaction();
                    $sql = "UPDATE user_data SET profiency=:profiency WHERE username=:username";             
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_profiency = $newProfiency;
                    $param_username = $this->username;
                    $stmt->execute();
                    
                    $sql2 = "UPDATE stockpile SET amount=:amount WHERE username=:username AND item='gold'";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_amount = $newBalance;
                    $param_username = $this->username;
                    $stmt2->execute();
                    $this->conn->commit();
                }
                catch(Exception $e) {
                    $this->conn->rollBack();
                    new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                    $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                    return false;
                }
            }
            $this->gameMessage("You have succesfully changed profiency to " . $newProfiency, true);
            $_SESSION['gamedata']['profiency'] = $newProfiency;
            unset($this->conn);
        }
    }
?>