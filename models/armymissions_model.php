<?php
    class armyMissions_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData() {
            $sql = "SELECT mission_id, location, required_warriors, mission, reward, time FROM armymissions";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data = array();
            $data['armyMissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $data['armyMissions'] = 'none';
            }
            $this->closeConn();
            return $data;
        }
        
        public function getCountdown() {
            $sql = "SELECT mission_countdown, mission FROM warrior WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['mission_countdown']);
            $date = date_timestamp_get($datetime);
            $this->closeConn();
            js_echo(array($date, $row['mission']));
        }
        
        public function getWarriors() {
            $warrior_id = array();
            $warrior_id[0] = $this->username;
            $sql = "SELECT warrior_id, type FROM warriors WHERE fetch_report='0' AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach($row as $key) {
                array_push($warrior_id, $key['warrior_id']);
            }
            $in  = str_repeat('?,', count($warrior_id) - 2) . '?';
            $sql = "SELECT stamina_level, technique_level, precision_level, strength_level FROM warrior_levels
            WHERE username= ? AND warrior_id IN ($in) ";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($warrior_id);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
            for($i = 0; $i < count($row2); $i++) {
                echo "Warrior: " . $row[$i]['warrior_id'] . '|';
                echo "Type: " . $row[$i]['type'] . '|';
                echo "Stamina level: " . $row2[$i]['stamina_level'] . '|';
                echo "Technique level: " . $row2[$i]['technique_level'] . '|';
                echo "Precision level: " . $row2[$i]['precision_level'] . '|';
                echo "Strength level: " . $row2[$i]['strength_level'] . '||';
            }
        }
    }
?>