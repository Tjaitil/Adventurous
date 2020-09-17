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
            $sql = "SELECT mission_id, location, required_warriors, mission, reward, time FROM armymissions";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data = array();
            $data['armyMissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $data['armyMissions'] = 'none';
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
                
            $sql = "SELECT
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
    }
?>