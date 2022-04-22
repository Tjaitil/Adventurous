<?php
    class UpdateArmymission_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function updateMission() {

            $param_username = $this->username;
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['mission'] == 0) {
                $this->response->addTo("errorGameMessage" , "You do not currently have any active army mission");
                return false;
            }
            
            $sql = "SELECT warrior_id, type FROM warriors WHERE mission=1 AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $warriors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $query_array = array_column($warriors, 'warrior_id');
            $query_array[] = $this->username;
            $in = str_repeat('?,', count($query_array) - 2) . '?';
            
            $sql = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp FROM warriors_levels
            WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $i = 0;
            $warrior_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Merge the arrays
            for($i = 0; $i < count($warrior_levels); $i++) {
                $warriors[$i] = array_merge($warriors[$i], $warrior_levels[$i]);
            }
            $param_mission_id = $row['mission'];
            $sql = "SELECT difficulty, reward, combat FROM armymissions WHERE mission_id=:mission_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mission_id", $param_mission_id, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            switch($row2['difficulty']) {
                case 'easy':
                    $xp = 100;
                    $warrior_xp = 30;
                    break;
                case 'medium':
                    $xp = 297;
                    $warrior_xp = 50;
                    break;
                case 'hard':
                    $xp = 512;
                    $warrior_xp = 100;
                    break;
                default:
                    $xp = 0;
                    $warrior_xp = 0;
                    break;
            }
            if($row2['combat'] == 1) {
                $combat_model = $this->loadModel('combat', true, true);
                $this->response->addTo("html", $combat_model->calculate(array("route" => "army mission", "difficulty" => $row2['difficulty'])));
            }
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warrior SET mission=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                

                $param_username = $this->username;
                for($i = 0; $i < count($warriors); $i++) {
                    if($warriors[$i]['type'] == 'melee') {
                        $param_stamina_xp = intval($warriors[$i]['stamina_xp']) + $warrior_xp;
                        $param_technique_xp = intval($warriors[$i]['technique_xp']) + $warrior_xp;
                        $param_strength_xp = intval($warriors[$i]['strength_xp']) + $warrior_xp;
                        $param_warrior_id = $warriors[$i]['warrior_id'];
                        $sql = "UPDATE warriors_levels SET stamina_xp=:stamina_xp, technique_xp=:technique_xp,
                                strength_xp=:strength_xp WHERE warrior_id=:warrior_id AND username=:username";
                        $stmt = $this->db->conn->prepare($sql);
                        $stmt->bindParam(":stamina_xp", $param_stamina_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":technique_xp", $param_technique_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":strength_xp", $param_strength_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    }
                    else {
                        $param_stamina_xp = intval($warriors[$i]['stamina_xp']) + $warrior_xp;
                        $param_technique_xp = intval($warriors[$i]['technique_xp']) + $warrior_xp;
                        $param_precision_xp = intval($warriors[$i]['precision_xp']) + $warrior_xp;
                        $param_warrior_id = $warriors[$i]['warrior_id'];
                        $sql = "UPDATE warriors_levels SET stamina_xp=:stamina_xp, technique_xp=:technique_xp, 
                                precision_xp=:precision_xp WHERE warrior_id=:warrior_id AND username=:username";
                        $stmt = $this->db->conn->prepare($sql);
                        $stmt->bindParam(":stamina_xp", $param_stamina_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":technique_xp", $param_technique_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":precision_xp", $param_precision_xp, PDO::PARAM_INT);
                        $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    }
                    $stmt->execute();
                }
                
                $sql3 = "UPDATE warriors SET mission=0 WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already is already defined in statement 1
                $stmt3->execute();
                
                // // Update inventory
                $this->UpdateGamedata->updateInventory('gold', $row2['reward']);
                // Only gain xp when warrior level is below 30 or if profiency is warrior
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                    $this->response->addTo("levelUP", $this->UpdateGamedata->updateXP('warrior', $xp));
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Armymission completed, {$xp} xp gained and {$row2['reward']} gold earned");
        }
        public function cancelMission() {
            $param_username = $this->username;
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['mission'] == 0) {
                $this->response->addTo("errorGameMessage", "Your warriors are not on a mission");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                
                $param_username = $this->username;
                $sql = "UPDATE warrior SET mission=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_mission = $row['mission'];
                $param_username = $this->username;
                $sql = "UPDATE warriors SET mission=0 WHERE mission=mission AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Mission has been cancelled");
        }
    }
?>