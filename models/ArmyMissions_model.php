<?php
    class ArmyMissions_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData($ajax) {
            $data = array();
            $now = new DateTime("now");
            $date = $now->format("Y-m-d");
            $param_date = $date;
            $sql = "SELECT mission_id, time, date FROM armymissions LIMIT 1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
            $stmt->execute();
            $data['armyMissions'] = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $data['armyMissions'] = 'none';
            }
            $db_date = (isset($data['armyMissions']['date'])) ? new DateTime($data['armyMissions']['date']) : 0;
            if($db_date->format("Y-m-d") < $now->format("Y-m-d")) {
                $this->generateNewAssignments();
            }
            $sql = "SELECT mission_id, required_warriors, mission, difficulty, reward, time, date, combat, location 
                    FROM armymissions
                    WHERE DATE(date)=:date";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
            $param_date = $date;
            $stmt->execute();
            $data['armyMissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i = 0; $i < count($data['armyMissions']); $i++) {
                $data['armyMissions'][$i]['time'] = round($data['armyMissions'][$i]['time'] / 60); 
            }
            $warriors_model = $this->loadModel('Warriors', true, true);
            $response = $warriors_model->getWarriors($ajax);
            $this->db->closeConn();
            if($ajax) {
                $this->response->addTo('html', $response);
            } else {
                $data['templateWarriorSelect'] = $response;
                return $data;
            }
        }
        public function getCountdown() {
            $param_username = $this->username;
            $sql = "SELECT mission_countdown, mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['mission_countdown']);
            $date = date_timestamp_get($datetime);
            $this->response->addTo("data", $date, array("index" => "date"));
            $this->response->addTo("data", $row['mission'], array("index" => "mission"));
            $this->db->closeConn();            
        }
        private function generateNewAssignments() {
            function ArrayMaker($array) {
                $locations = array("tasnobil", "krasnur");
                return array("mission" => $array[0], "time" => $array[1],
                             "reward" => $array[2], "required_warriors" => $array[3], 
                             "combat" => $array[4], "location" => $locations[rand(0, 1)]);
            }
            $new_missions = array();
            
            // Determine how many missions of each difficulty by using random function
            $easy_missions_amount = 5;
            $medium_missions_amount = 3; 
            $hard_missions_amount = 2;
            $medium_rate = 1.3;
            $hard_rate = 2.1;
            
            $missions = array();
            $missions[] = arrayMaker(array("Accompany merchants from fansal-plains to fagna", 1000, 250, 3, 1));
            $missions[] = arrayMaker(array("Scout for daqloons and Wilsnas shore ", 1000, 250, 3, 1));
            $missions[] = arrayMaker(array("Protect Duke Howling", 2000, 200, 4, 1));
            $missions[] = arrayMaker(array("Patrol the kingdom of megles", 1000, 200, 3, 0));
            $missions[] = arrayMaker(array("Patrol the kingdom of towheren", 1000, 250, 3, 0));
            $missions[] = arrayMaker(array("Acconmpany Hildi on her tour north of Lenia Bridge", 1000, 250, 2, 0));
            $missions[] = arrayMaker(array("Protect mines in Golbak", 1000, 400, 3, 1));
            $missions[] = arrayMaker(array("Set up outpost at Byshli islands", 2500, 300, 5, 1));
            $missions[] = arrayMaker(array("Protect trading routes form Ter", 2000, 1000, 3, 1));
            $missions[] = arrayMaker(array("Guard mines in snerpiir", 3000, 600, 3, 1));
            $missions[] = arrayMaker(array("Search Heskil mountains for bandits", 3000, 700, 5, 0));
            $missions[] = arrayMaker(array("Guard the docks at fagna", 2598, 500, 3, 0));
            $missions[] = arrayMaker(array("Guard the diplomat from fagna heading to hirtam", 4000, 600, 4, 1));
            $missions[] = arrayMaker(array("Patrol around daqloon islands", 5000, 460, 3, 1));
            $missions[] = arrayMaker(array("Patrol around fansal-plains", 6000, 600, 3, 1));
            $missions[] = arrayMaker(array("Protect traders heading to fagna from north", 4000, 500, 3, 0));
            $missions[] = arrayMaker(array("Guard Lenia bridge", 3000, 600, 3, 0));
            $missions[] = arrayMaker(array("Protect fishermen of the coast of towhar", 2500, 600, 3, 1));
            $missions[] = arrayMaker(array("Guard the docks at Towhar", 2500, 560, 2, 0));
            $missions[] = arrayMaker(array("Prevent smuggling routes of the coast of fansal-plains", 4000, 600, 3, 0));
            $missions[] = arrayMaker(array("Locate bandit hideouts", 4500, 800, 3, 0));
            $missions[] = arrayMaker(array("Retrieve stolen merchandise on the Byshli islands", 5000, 800, 3, 0));
            $missions[] = arrayMaker(array("Patrol the streets of Towhar", 4000, 200, 2, 0));
            $missions[] = arrayMaker(array("Patrol the streets of Cruendo", 4000, 250, 3, 0));
            $missions[] = arrayMaker(array("Patrol around Khanz", 2000, 300, 3, 1));
            $missions[] = arrayMaker(array("Protect the caravan with supplies to Khanz", 3000, 200, 3, 1));
            $missions[] = arrayMaker(array("Protect Hirtam from pirates", 4500, 700, 3, 0));
            $missions[] = arrayMaker(array("Protect Pvitul from pirates", 4500, 700, 3, 0));
            $missions[] = arrayMaker(array("Guard Tibs pass", 4250, 800, 3, 1));
            $missions[] = arrayMaker(array("Scout for Daqloons activity on Wilsnas shore", 3500, 700, 3, 1));
            
            // armyMissions
            for($i = 0; $i < ($easy_missions_amount + $medium_missions_amount + $hard_missions_amount); $i++) {
                // Select random mission from $missions array
                $mission = $missions[array_rand($missions)];
                if($i < ($easy_missions_amount)) {
                    // Make missions with easy difficulty
                    $mission['difficulty'] = "easy";
                    $mission['warriors_required'] = 3;
                }
                else if($i < ($easy_missions_amount + $medium_missions_amount)) {
                    // Make missions with medium difficulty
                    $mission['difficulty'] = "medium";
                    $mission['reward'] =  intval($mission['reward']) * $medium_rate;
                    $mission['time'] = intval($mission['time']) * $medium_rate;
                    $mission['warriors_required'] = 6; 
                }
                else {
                    // Make missions with hard difficulty
                    $mission['difficulty'] = "hard";
                    $mission['reward'] =  intval($mission['reward']) * $hard_rate;
                    $mission['time'] = intval($mission['time']) * $hard_rate;
                    $mission['warriors_required'] = 10;
                }
                $new_missions[] = $mission;
            }
            $sql = "SELECT mission FROM warrior WHERE NOT mission=0";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $active_mission_id = array_column($row, 'mission');
            $in  = str_repeat('?,', count($active_mission_id) - 1) . '?';
            try {
                $this->db->conn->beginTransaction();
            
                // Delete old assignments that no player is currently on
                    if($stmt->rowCount() > 0) {
                        $sql = "DELETE FROM armymissions WHERE mission_id NOT IN ($in)";
                    }
                    else {
                        $sql = "DELETE FROM armymissions";    
                    }
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($active_mission_id);
                // If no rows has been affected, throw error;
                if($stmt->rowCount() === 0) {
                    throw new Exception("No rows deleted from delete query " . __METHOD__);
                }
                $param_required_warriors = "";
                $param_mission = "";
                $param_difficulty = "";
                $param_reward = 0;
                $param_time = 0;
                $param_combat = 0;
                $param_location = "";
                // Insert new assignments
                $sql = "INSERT INTO armymissions (required_warriors, mission, difficulty, reward, time, combat, location) 
                        VALUES(:required_warriors, :mission, :difficulty, :reward, :time, :combat, :location)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":required_warriors", $param_required_warriors, PDO::PARAM_INT);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt->bindParam(":reward", $param_reward, PDO::PARAM_INT);
                $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
                $stmt->bindParam(":combat", $param_combat, PDO::PARAM_INT);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_INT);
                foreach($new_missions as $key => $value) {
                    // $value = mission array, bind parameters
                        $param_required_warriors = $value['required_warriors'];
                        $param_mission = $value['mission'];
                        $param_difficulty = $value['difficulty'];
                        $param_reward = $value['reward'];
                        $param_time = $value['time'];
                        $param_combat = $value['combat'];
                        $param_location = $value['location'];
                        $stmt->execute();
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->errorHandler->reportError(array($this->username, $e->getMessage()));
                return false;
            }
        }
    }
?>