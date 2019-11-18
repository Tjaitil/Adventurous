<?php
    class armyMissions_model extends model {
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
            $warrior_id = array();
            $warrior_id[0] = $this->username;
            $sql = "SELECT warrior_id, type FROM warriors WHERE fetch_report = '0' AND mission = '0' AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $warriors = array();
            if($stmt->rowCount() > 0) {
                foreach($row as $key) {
                    array_push($warrior_id, $key['warrior_id']);
                }
                $in  = str_repeat('?,', count($warrior_id) - 2) . '?';
                $sql = "SELECT warrior_id, stamina_level, technique_level, precision_level, strength_level FROM warrior_levels
                WHERE username= ? AND warrior_id IN ($in) ";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($warrior_id);
                $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $sql = "SELECT
                        (SELECT SUM(attack) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                        (SELECT SUM(defence) FROM smithy_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                        FROM warrior_armory
                        WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->db->closeConn();
                
                foreach($row2 as $key => $value) {
                    array_push($warriors, array_merge($row[$key], $row2[$key], $row3[$key]));
                }
            }
            get_template('warrior_select', $warriors, true);
        }
    }
?>