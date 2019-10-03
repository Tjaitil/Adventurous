<?php
    class SetArmymission_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function setMission($mission_id, $warrior_status) {
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['mission'] > 0) {
                $this->gameMessage("ERROR: You already have a mission");
                return false;
            }
            
            $sql = "SELECT location, required_warriors, mission, time FROM armymissions WHERE mission_id=:mission_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mission_id", $param_mission_id, PDO::PARAM_STR);
            $param_mission_id = $mission_id;
            $stmt->execute();
            $mission_data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $queryArray = explode(",", $warrior_status);
            $queryArray[] = $this->username;
            
            $in  = str_repeat('?,', count($queryArray) - 2) . '?';
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id IN ($in) AND fetch_report=0 AND username=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($queryArray);
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $row_count = $stmt->rowCount();
            if($mission_data['required_warriors'] != $row_count) {
                $this->gameMessage("ERROR: You have not selected the right amount of warriors");
                return false;
            }
            $add_time = $mission_data['time'];
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$add_time} seconds");
            $mission_countdown = date_format($new_date, "Y-m-d H:i:s");
            
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE warrior SET warrior_xp=:warrior_xp, mission=:mission, mission_countdown=:mission_countdown
                        WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_STR);
                $stmt->bindParam(":mission_countdown", $param_mission_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_xp = $this->session['warrior']['xp'] + (10 * $mission_data['required_warriors']);
                $param_mission = $mission_id;
                $param_mission_countdown = $mission_countdown;
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE user_levels SET warrior_xp=:warrior_xp WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_warrior_xp AND $param_username already defined in statement 1;
                $stmt2->execute();
                
                $sql3 = "UPDATE warriors SET mission=1 WHERE warrior_id IN ($in) AND username=?";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->execute($queryArray);
        
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false; 
            }
            $this->db->closeConn();
            $_SESSION['gamedata']['warrior']['warrior_xp'] = $param_warrior_xp;
            $this->gameMessage("You have a new mission");
        }
    }
?>