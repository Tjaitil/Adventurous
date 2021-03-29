<?php
    class Tavern_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->errorHandler->reportError(array($this->username, "Not valid location: " . $this->session['location'] . __METHOD__));
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }            
            $sql = "SELECT new_workers, {$this->session['location']} FROM tavern_times WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['workers'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT name from persons WHERE location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $stmt->execute();
            $data['persons'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $data;
        }
        public function resetData() {
            // Function to reset the tavern times when there is a new day
            try {
                $this->db->conn->beginTransaction();
                $sql = "DELETE FROM tavern_workers WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE tavern_times SET towhar=0, krasnur=0, snerpiir=0, golbak=0, tasnobil=0, parth=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
        }
        public function getWorkers() { 
            $sql = "SELECT type, level FROM tavern_workers WHERE city=:city AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $row;
        }
        public function updateWorkers($workers) {
            $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
            if (array_search($this->session['location'], $cities) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            //Delete yesterdays workers
            $sql = "DELETE FROM tavern_workers WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            
            try {
                $this->db->conn->beginTransaction();
                
                //Set $city to 1 so that the game doesn't generate new workers for the city;
                $sql = "UPDATE tavern_times SET new_workers=:new_workers, {$this->session['location']}=1 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":new_workers", $param_new_workers, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_new_workers = date("Y-m-d");
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "INSERT INTO tavern_workers (username, city, type, level)
                         VALUES(:username, :city, :type, :level);";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt2->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt2->bindParam(":level", $param_level, PDO::PARAM_INT);
                $param_username = $this->username;
                $param_city = $this->session['location'];
                foreach($workers as $key) {
                    $param_type = $key['type'];
                    if(isset($key['level'])) {
                        $param_level = $key['level'];
                    }
                    else {
                        $param_level = 0;
                    }
                    $stmt2->execute();
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
        }
        public function getHealingAmount($GET) {
            $item = strtolower($GET['item']);
            
            $sql = "SELECT heal FROM bakery_data WHERE type=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $item;
            $stmt->execute();
            // Check if returned rows are greater than 0, if so return heal value or else 0
            if($stmt->rowCount() > 0) {
                echo $stmt->fetch(PDO::FETCH_OBJ)->heal;
            }
            else {
                echo 0;
            }
        }
    }
?>