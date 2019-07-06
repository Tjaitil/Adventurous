<?php
    class tavern_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getPersons() {
            $sql = "SELECT name from persons WHERE location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row == false) {
                return array();
            }
            $this->closeConn();
            return $row;
        }
        
        public function getData() {
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            $sql = "SELECT new_workers, " . $this->session['location'] . " FROM tavern_times WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row;
        }
        
        public function getWorkers() {
            $sql = "SELECT farmer_amount, miner_amount FROM tavern_workers WHERE city=:city AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_city = $this->session['location'];
            $stmt->execute();
            $data = array ();
            $data['tavern_workers'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT type, level FROM tavern_warriors WHERE city=:city AND username=:username ";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['tavern_warriors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $data;
        }
        
        public function updateWorkers($farmers, $miners, $warriors) {
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            //Delete yesterdays workers
            $sql = "DELETE FROM tavern_workers WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            
            $sql = "DELETE FROM tavern_warriors WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            
            try {
                $this->conn->beginTransaction();
                
                //Set $city to 1 so that the game doesn't generate new workers for the city;
                $sql = "UPDATE tavern_times SET new_workers=:new_workers, " . $this->session['location'] ."=1 WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":new_workers", $param_new_workers, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_new_workers = date("Y-m-d");
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "INSERT INTO tavern_workers (username, city, farmer_amount, miner_amount)
                         VALUES(:username, :city, :farmer_amount, :miner_amount);";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt2->bindParam(":farmer_amount", $param_farmer_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":miner_amount", $param_miner_amount, PDO::PARAM_STR);
                $param_username = $this->username;
                $param_city = $this->session['location'];
                $param_farmer_amount = $farmers;
                $param_miner_amount = $miners;
                $stmt2->execute();
                
                require(constant("ROUTE_HELPER") . 'warrior_insert.php');
                for($i = 0; $i < count($warriors); $i++) {
                    warrior_insert($warriors[$i], $this->session['location'], $this->conn, $this->username);
                }                
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
        }
    }
?>