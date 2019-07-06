<?php
    class adventurestatus_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function startAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessages("ERROR! You currently have no adventure!", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $adventure_id = $row['adventure_id'];
            
            $sql = "SELECT difficulty, location, adventure_leader, farmer, miner, warrior, trader FROM adventures
                    WHERE adventure_id=:adventure_id AND adventure_leader=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessages("You do not have a adventure to start!", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT status FROM adventures_farmer WHERE username=:username AND adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            // $param_adventure_id is already defined!
            $param_username = $row['farmer'];
            $stmt->execute();
            $farmer_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT status FROM adventures_miner WHERE username=:username AND adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            // $param_adventure_id is already defined!
            $param_username = $row['miner'];
            $stmt->execute();
            $miner_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT status FROM adventures_trader WHERE username=:username AND adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            // $param_adventure_id is already defined!
            $param_username = $row['trader'];
            $stmt->execute();
            $trader_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT status FROM adventures_warrior WHERE username=:username AND adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            // $param_adventure_id is already defined!
            $param_username = $row['warrior'];
            $stmt->execute();
            $warrior_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if(in_array(0 , array($farmer_data['status'] && $miner_data['status'] && $trader_data['status'] && $warrior_data['status']))
               != false) {
                $sql = "SELECT time FROM adventures_data WHERE difficulty=:difficulty AND location=:location";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $param_difficulty = $row['difficulty'];
                $param_location = $row['location'];
                $stmt->execute();
                $time = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $time = intval($time['time']);
                $date = date("Y-m-d H:i:s");
                $new_date = new DateTime($date);
                $new_date->modify("+{$time}seconds");
                $adventure_countdown = date_format($new_date, "Y-m-d H:i:s");

                $queryArray = array();
                array_push($queryArray, $row['farmer'], $row['miner'], $row['trader'], $row['warrior']);
                $in  = str_repeat('?,', count($queryArray) - 1) . '?';
                try {
                    $this->conn->beginTransaction();
                    $sql = "UPDATE adventures SET adventure_countdown=:adventure_countdown, adventure_status=1
                            WHERE adventure_id=:adventure_id";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":adventure_countdown", $param_adventure_countdown, PDO::PARAM_STR);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                    $param_adventure_countdown = $adventure_countdown;
                    //$param_adventure_id is already defined!
                    $stmt->execute();
                    
                    $sql2 = "UPDATE adventure SET adventure_status=1 WHERE username IN ($in)";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->execute($queryArray);
    
                    $this->conn->commit();
                }
                catch(Exception $e) {
                    $this->conn->rollBack();
                    new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                    $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                    return false;
                }
                $_SESSION['gamedata']['adventure_status'] = 1;
                $this->closeConn();
                echo "You have started the adventure!";
            }
        }
    }
?>