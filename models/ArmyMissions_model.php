<?php
class ArmyMissions_model extends Model
{
    use ModelFactory;

    public $username;
    public $session;

    function __construct($session)
    {
        parent::__construct();
        $this->commonModels(true, false);

        $this->username = $session['username'];
        $this->session = $session;
    }

    /**
     * Retrieve army missions and active army missions for a user from database
     *
     * @param bool|string $js Bool
     * @return array $data Data from database
     */
    public function getData()
    {
        $param_username = $this->username;
        $sql3 = "SELECT avail_workforce, efficiency_level FROM farmer_workforce WHERE username=:username";
        $stmt3 = $this->db->conn->prepare($sql3);
        $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt3->execute();

        $data = array();
        $now = new DateTime("now");
        $date = $now->format("Y-m-d");
        $param_date = $date;

        // Check date of missions
        $sql = "SELECT date FROM army_missions ORDER BY date DESC LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $data['armyMissionTest'] = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$stmt->rowCount() > 0) {
            $data['armyMissionTest'] = 'none';
        }
        $db_date = (isset($data['armyMissionTest']['date']))
            ? new DateTime($data['armyMissionTest']['date']) : 0;
        if ($db_date === 0 || $db_date->format("Y-m-d") < $now->format("Y-m-d")) {
            $this->generateNewAssignments();
        }

        $param_username = $this->username;
        $sql = "SELECT a.required_warriors, a.mission_id, a.mission, a.difficulty, a.reward, 
                           a.combat, a.time, b.mission_countdown, a.location
                    FROM army_missions as a INNER JOIN
                    army_missions_active as b ON a.mission_id=b.mission_id 
                    WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $data['current_army_missions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "SELECT mission_id, required_warriors, mission, difficulty, reward, time, date, combat, location 
                    FROM army_missions
                    WHERE DATE(date)=:date ORDER BY difficulty";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
        $param_date = $date;
        $stmt->execute();
        $data['army_missions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        for ($i = 0; $i < count($data['army_missions']); $i++) {
            $data['army_missions'][$i]['time'] = round($data['army_missions'][$i]['time'] / 60);
        }

        $location = $this->session['location'];
        usort($data['army_missions'], function ($a, $b) use ($location) {
            $a_check = ($a["location"] === $location) ? 1 : -1;
            $b_check = ($b["location"] === $location) ? 1 : -1;
            return $b_check <=> $a_check;
        });

        return $data;
    }
    public function getCountdowns($GET)
    {
        $param_username = $this->username;
        $GET_mission_id_isset = isset($GET["mission_id"]);
        if ($GET_mission_id_isset) {
            $param_mission_id = $GET["mission_id"];
            $sql = "SELECT mission_countdown, mission_id FROM army_missions_active 
                    WHERE username=:username AND mission_id=:mission_id";
        } else {
            $sql = "SELECT mission_countdown, mission_id FROM army_missions_active 
                    WHERE username=:username";
        }

        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        if ($GET_mission_id_isset) $stmt->bindParam(":mission_id", $param_mission_id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data = array();
        foreach ($row as $key) {
            $datetime = date_timestamp_get(new DateTime($key['mission_countdown']));
            $data[] = [
                "datetime" => $datetime,
                "mission_id" => $key['mission_id'],
            ];
        }
        $this->response->addTo("data", $data, ["index" => "countdowns"]);
    }
    private function generateNewAssignments()
    {
        function ArrayMaker($array)
        {
            $locations = array("tasnobil", "krasnur");
            return array(
                "mission" => $array[0], "time" => $array[1],
                "reward" => $array[2], "required_warriors" => $array[3],
                "combat" => $array[4], "location" => $locations[rand(0, 1)]
            );
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

        for ($i = 0; $i < ($easy_missions_amount + $medium_missions_amount + $hard_missions_amount); $i++) {
            // Select random mission from $missions array
            $mission = $missions[array_rand($missions)];
            if ($i < ($easy_missions_amount)) {
                // Make missions with easy difficulty
                $mission['difficulty'] = "easy";
                $mission['warriors_required'] = 3;
            } else if ($i < ($easy_missions_amount + $medium_missions_amount)) {
                // Make missions with medium difficulty
                $mission['difficulty'] = "medium";
                $mission['reward'] =  intval($mission['reward']) * $medium_rate;
                $mission['time'] = intval($mission['time']) * $medium_rate;
                $mission['warriors_required'] = 6;
            } else {
                // Make missions with hard difficulty
                $mission['difficulty'] = "hard";
                $mission['reward'] =  intval($mission['reward']) * $hard_rate;
                $mission['time'] = intval($mission['time']) * $hard_rate;
                $mission['warriors_required'] = 10;
            }
            $new_missions[] = $mission;
        }
        try {
            $this->db->conn->beginTransaction();
            // Delete old assignments that no player is currently on and is passed max_date
            $sql = "DELETE a FROM army_missions AS a
                    LEFT JOIN army_missions_active AS b ON a.mission_id = b.mission_id  
                    WHERE b.mission_id IS NULL";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();

            $param_required_warriors = "";
            $param_mission = "";
            $param_difficulty = "";
            $param_reward = 0;
            $param_time = 0;
            $param_combat = 0;
            $param_location = "";
            // Insert new assignments
            $sql = "INSERT INTO army_missions (required_warriors, mission, difficulty, reward, time, combat, location) 
                        VALUES(:required_warriors, :mission, :difficulty, :reward, :time, :combat, :location)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":required_warriors", $param_required_warriors, PDO::PARAM_INT);
            $stmt->bindParam(":mission", $param_mission, PDO::PARAM_STR);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":reward", $param_reward, PDO::PARAM_INT);
            $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
            $stmt->bindParam(":combat", $param_combat, PDO::PARAM_INT);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            foreach ($new_missions as $key => $value) {
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
        } catch (Exception $e) {
            $this->db->conn->rollBack();
            $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
            return false;
        }
    }
}
