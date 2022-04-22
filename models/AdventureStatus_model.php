<?php
    class AdventureStatus_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function startAdventure() {
            $param_username = $this->username;
            $sql = "SELECT adventure_id, difficulty, location, adventure_leader, farmer, miner, warrior, trader, adventure_status
                    FROM adventures
                    WHERE adventure_leader=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You do not have a adventure to start!");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['adventure_status'] == 1) {
                $this->response->addTo("errorGameMessage", "Adventure already started!");
                return false;
            }
            
            $param_adventure_id = $row['adventure_id'];
            $sql = "SELECT status FROM adventure_requirements WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->execute();
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(in_array(0 , $row2) != false) {
                $this->response->addTo("errorGameMessage", "There is some requirements that are not met");
                return false;
            }
            $param_difficulty = $row['difficulty'];
            $param_location = $row['location'];
            $sql = "SELECT time FROM adventures_data WHERE difficulty=:difficulty AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
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
                $this->db->conn->beginTransaction();
             
                $param_adventure_countdown = $adventure_countdown;
                $param_adventure_id = $row['adventure_id'];
                $sql = "UPDATE adventures SET adventure_countdown=:adventure_countdown, adventure_status=1
                        WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_countdown", $param_adventure_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->execute();
                
                $sql2 = "UPDATE adventure SET adventure_status=1 WHERE username IN ($in)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->execute($queryArray);

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $_SESSION['gamedata']['adventure_status'] = 1;
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Adventure started!");
        }
    }
?>