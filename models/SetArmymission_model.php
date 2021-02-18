<?php
    class SetArmymission_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function setMission($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set army mission
            $mission_id = $POST['mission_id'];
            $warrior_status = $POST['warrior_check'];
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['mission'] > 0) {
                $this->gameMessage("ERROR: You already have a mission", true);
                return false;
            }
            
            $sql = "SELECT required_warriors, mission, difficulty, time FROM armymissions WHERE mission_id=:mission_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mission_id", $param_mission_id, PDO::PARAM_STR);
            $param_mission_id = $mission_id;
            $stmt->execute();
            $mission_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $required_warrior_level = array("easy" => 2, "medium" => 15, "hard" => 35);
            if($required_warrior_level[$mission_data['difficulty']] > $this->session['warrior']['level']) {
                $this->gameMessage("ERROR: This mission requires warrior level {$required_warrior_level[$mission_data['difficulty']]} ", true);
                return false;
            }
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
                $sql = "UPDATE warrior SET mission=:mission, mission_countdown=:mission_countdown
                        WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_INT);
                $stmt->bindParam(":mission_countdown", $param_mission_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mission = $mission_id;
                $param_mission_countdown = $mission_countdown;
                $param_username = $this->username;
                $stmt->execute();
                
                // Only gain xp when warrior level is below 30 or if profiency is warrior
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                    $this->UpdateGamedata->updateXP('warrior', (10 * $mission_data['required_warriors']));
                }
                
                $sql3 = "UPDATE warriors SET mission=1 WHERE warrior_id IN ($in) AND username=?";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->execute($queryArray);
        
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>