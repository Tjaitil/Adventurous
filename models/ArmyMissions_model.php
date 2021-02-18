<?php
    class ArmyMissions_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            $sql = "SELECT mission_id, required_warriors, mission, difficulty, reward, time, date FROM armymissions";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['armyMissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $data['armyMissions'] = 'none';
            }
            $now = new DateTime("now");
            $db_date = new DateTime($data['armyMissions'][0]['date']);
            if($db_date->format("Y-m-d") < $now->format("Y-m-d")) {
                $this->generateNewAssignments();
                $sql = "SELECT mission_id, required_warriors, mission, difficulty, reward, time, date FROM armymissions";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $data['armyMissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            for($i = 0; $i < count($data['armyMissions']); $i++) {
                $data['armyMissions'][$i]['time'] = round($data['armyMissions'][$i]['time'] / 60); 
            }
            $this->db->closeConn();    
            return $data;
        }
        public function getCountdown() {
            $sql = "SELECT mission_countdown, mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['mission_countdown']);
            $date = date_timestamp_get($datetime);
            $this->db->closeConn();
            
            js_echo(array($date, $row['mission']));
        }
        public function getWarriors() {
            $warriors = array();
            
            $sql = "SELECT w.warrior_id, w.type, wl.stamina_level, wl.technique_level, wl.precision_level, wl.strength_level
                            FROM warriors as w
                            INNER JOIN warriors_levels as wl ON wl.warrior_id = w.warrior_id AND wl.username = w.username
                            WHERE w.mission=0 AND w.training_type='none' AND w.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
            $query_array = array_column($row, 'warrior_id');
            array_unshift($query_array, $this->username);
            $in  = str_repeat('?,', count($query_array) - 2) . '?';
                
            $sql = "SELECT warrior_id, helm, ammunition, ammunition_amount, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                    (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                    FROM warrior_armory
                    WHERE username= ? AND warrior_id IN ($in)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            
            foreach($row2 as $key => $value) {
                array_push($warriors, array_merge($row[$key], $row2[$key]));
            }
            get_template('warrior_select', $warriors, true);
        }
        private function generateNewAssignments() {
            function ArrayMaker($array) {
                return array("mission" => $array[0], "time" => $array[1],
                             "reward" => $array[2], "required_warriors" => $array[3]);
            }
                
            $new_missions = array();
            
            // Determine how many missions of each difficulty by using random function
            $easy_missions_amount = 5;
            $medium_missions_amount = 3; 
            $hard_missions_amount = 2;
            $medium_rate = 1.3;
            $hard_rate = 2.1;
            
            // Determine how many trader assigments of each difficulty by using random function
            $easy_assignment_amount;
            $medium_assignment_amount;
            $hard_assigmnent_amount;
            $favor_assignment_amount;
            
            $missions = array();
            $missions[] = arrayMaker(array("Accompany merchants from fansal-plains to fagna", 1000, 250, 3));
            $missions[] = arrayMaker(array("Scout for daqloons and Wilsnas shore ", 1000, 250, 3));
            $missions[] = arrayMaker(array("Protect Duke Howling", 2000, 200, 4));
            $missions[] = arrayMaker(array("Patrol the kingdom of megles", 1000, 200, 3));
            $missions[] = arrayMaker(array("Patrol the kingdom of towheren", 1000, 250, 3));
            $missions[] = arrayMaker(array("Acconmpany Hildi on her tour north of Lenia Bridge", 1000, 250, 2));
            $missions[] = arrayMaker(array("Protect mines in Golbak", 1000, 400, 3));
            $missions[] = arrayMaker(array("Set up outpost at Byshli islands", 2500, 300, 5));
            $missions[] = arrayMaker(array("Protect trading routes form Ter", 2000, 1000, 3));
            $missions[] = arrayMaker(array("Guard mines in snerpiir", 3000, 600, 3));
            $missions[] = arrayMaker(array("Search Heskil mountains for bandits", 3000, 700, 5));
            $missions[] = arrayMaker(array("Guard the docks at fagna", 2598, 500, 3));
            $missions[] = arrayMaker(array("Guard the diplomat from fagna heading to hirtam", 4000, 600, 4));
            $missions[] = arrayMaker(array("Patrol around daqloon islands", 5000, 460, 3));
            $missions[] = arrayMaker(array("Patrol around fansal-plains", 6000, 600, 3));
            $missions[] = arrayMaker(array("Protect traders heading to fagna from north", 4000, 500, 3));
            $missions[] = arrayMaker(array("Guard Lenia bridge", 3000, 600, 3));
            $missions[] = arrayMaker(array("Protect fishermen of the coast of towhar", 2500, 600, 3));
            $missions[] = arrayMaker(array("Guard the docks at Towhar", 2500, 560, 2));
            $missions[] = arrayMaker(array("Stop smuggling routes of the coast of fansal-plains", 4000, 600, 3));
            $missions[] = arrayMaker(array("Locate bandit hideouts", 4500, 800, 3));
            $missions[] = arrayMaker(array("Retrieve stolen merchandise on the Byshli islands", 5000, 800, 3));
            $missions[] = arrayMaker(array("Patrol the streets of Towhar", 4000, 200, 2));
            $missions[] = arrayMaker(array("Patrol the streets of Cruendo", 4000, 250, 3));
            $missions[] = arrayMaker(array("Patrol around Khanz", 2000, 300, 3));
            $missions[] = arrayMaker(array("Protect the caravan with water supply to Khanz", 3000, 200, 3));
            $missions[] = arrayMaker(array("Protect Hirtam from pirates", 4500, 700, 3));
            $missions[] = arrayMaker(array("Protect Pvitul from pirates", 4500, 700, 3));
            $missions[] = arrayMaker(array("Guard Tibs pass", 4250, 800, 3));
            $missions[] = arrayMaker(array("Scout for Daqs activity on Wilsnas shore", 3500, 700, 3));
            
            // armyMissions
            for($i = 0; $i < ($easy_missions_amount + $medium_missions_amount + $hard_missions_amount); $i++) {
                // Select random mission from $missions array
                $mission = $missions[array_rand($missions)];
                if($i < ($easy_missions_amount)) {
                    // Make missions with easy difficulty
                    $mission['difficulty'] = "easy";
                }
                else if($i < ($easy_missions_amount + $medium_missions_amount)) {
                    // Make missions with medium difficulty
                    $mission['difficulty'] = "medium";
                    $mission['reward'] =  intval($mission['reward']) * $medium_rate;
                    $mission['time'] = intval($mission['time']) * $medium_rate;
                    $mission['warriors_required'] = round(intval($mission['required_warriors']) * $medium_rate);
                }
                else {
                    // Make missions with hard difficulty
                    $mission['difficulty'] = "hard";
                    $mission['reward'] =  intval($mission['reward']) * $hard_rate;
                    $mission['time'] = intval($mission['time']) * $hard_rate;
                    $mission['warriors_required'] = round(intval($mission['required_warriors']) * $hard_rate) + 2;
                }
                $new_missions[] = $mission;
            }
            try {
                $this->db->conn->beginTransaction();
                
                // Delete old assignments
                $sql = "DELETE FROM armymissions";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute();
                // If no rows has been affected, throw error;
                if($stmt->rowCount() === 0) {
                    throw new Exception("No rows deleted from delete query " . __METHOD__);
                }

                // Insert new assignments
                $sql = "INSERT INTO armymissions (required_warriors, mission, difficulty, reward, time) 
                        VALUES(:required_warriors, :mission, :difficulty, :reward, :time)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":required_warriors", $param_required_warriors, PDO::PARAM_INT);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt->bindParam(":reward", $param_reward, PDO::PARAM_INT);
                $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
                foreach($new_missions as $key => $value) {
                    // $value = mission array, bind parameters
                        $param_required_warriors = $value['required_warriors'];
                        $param_mission = $value['mission'];
                        $param_difficulty = $value['difficulty'];
                        $param_reward = $value['reward'];
                        $param_time = $value['time'];
                        $stmt->execute();
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn-rollBack();
                $this->errorHandler->reportError(array($this->username, $e->getMessage()));
                return false;
            }
        }
    }
?>