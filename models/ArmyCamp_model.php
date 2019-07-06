<?php
    class ArmyCamp_model extends model {
        public $username;
        public $session;
        
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        // Function to echo date for ajax request
        public function getCountdown() {
            $sql = "SELECT training_countdown, fetch_report FROM warriors WHERE location=:location AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
            $data = array();
            foreach($row as $key) {
                $datetime = new DateTime($key['training_countdown']);
                $date = date_timestamp_get($datetime);
                echo $date . "|" . $key['fetch_report'] . "||";
            }
        }
        
        public function getData($js = false) {
            $data = array();
            $sql = "SELECT a.warrior_id, a.type, b.stamina_level, b.stamina_xp, b.technique_level, b.technique_xp, b.precision_level,
                    b.precision_xp, b.strength_level, b.strength_xp
                    FROM warriors AS a INNER JOIN warrior_levels AS b ON b.username = a.username
                    WHERE a.location=:location AND a.username=:username GROUP BY a.warrior_id;";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $stmt->execute();
            $data['warrior_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($js === true) {
                get_template('armycamp', $data, true);
            }
            else {
                return $data;
            }
        }
        
        public function checkWarriorLevel($warrior_levels) {
            $level_up = array();
            if(count($warrior_levels) === 0) {
                return $level_up;
            }
            $values = array();
            // The foreach gets every unique level from the soldier, it then returns the values and pushes it unto the $values array
            foreach($warrior_levels as $key) {
                
                unset($key['warrior_id'], $key['type'], $key['stamina_xp'], $key['technique_xp'], $key['precision_xp'], $key['strength_xp']);
                $unique = array_unique($key);
                $value = array_values($unique);
                array_push($values, $value);
            }
            // $combined merges everything that is inside $values to make 1 array and check for last time for unique items
            $combined = array_values(array_unique(array_merge(...$values)));
            // $in makes a prepared statement string by replacing the value with a ? to get multiple values;
            $in  = str_repeat('?,', count($combined) - 1) . '?';
            $sql = "SELECT skill_level, next_level FROM warriors_level_data WHERE skill_level IN ($in)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($combined);
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $level_info = array();
            foreach($row as $key) {
                $level_info[$key['skill_level']] = $key['next_level'];
            }
            
            $statements = array();
            $skills = array('stamina', 'technique', 'precision', 'strength');
            foreach($warrior_levels as $key) {
                for($i = 0; $i < count($skills); $i++) {
                    if($level_info[$key[$skills[$i] . '_level']] <= $key[$skills[$i].'_xp']) {
                        if($i === 0 ) {
                            $level_up[$key['warrior_id']]['update'] = "UPDATE warrior_levels SET ";
                        }
                        $new_level = $key[$skills[$i] . '_level'] + 1;
                        $level_up[$key['warrior_id']]['update'] .= $skills[$i] . '_level' . '='. $new_level .',';
                        $level_up[$key['warrior_id']]['new_' . $skills[$i]. '_level'] = $skills[$i] . '=' . $new_level;
                    }
                }
                if(isset($level_up[$key['warrior_id']]['update'])) {
                        $level_up_statement = rtrim($level_up[$key['warrior_id']]['update'], ",");
                        $level_up_statement .= " WHERE warrior_id=" . $key['warrior_id'] . " AND username=:username";
                        $statements[] = $level_up_statement;
                        unset($level_up[$key['warrior_id']]['update']);
                    }
            }
    
            if(count($level_up) > 0) {
                try {
                    $this->conn->beginTransaction();
                    
                    require(constant("ROUTE_HELPER") . 'warrior_update.php');
                    for($i = 0; $i < count($statements); $i++ ) {
                        warrior_update($statements[$i], $this->conn, $this->username);
                    }
                    
                    $sql2 = "UPDATE warrior SET warrior_xp=:warrior_xp WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_warrior_xp = $_SESSION['gamedata']['warrior']['warrior_xp'] + 20;
                    $param_username = $this->username;
                    $stmt2->execute();
                    
                    $sql3 = "UPDATE user_levels SET warrior_xp=:warrior_xp WHERE username=:username";
                    $stmt3 = $this->conn->prepare($sql3);
                    $stmt3->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                    $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                    //$param_warrior_xp and $param_username already defined;
                    $stmt3->execute();
                    
                    $this->conn->commit();
                }
                catch(Exception $e) {
                    $this->conn->rollBack();
                    new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                    $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                    return false;
                }
                $_SESSION['gamedata']['warrior']['warrior_xp'] = $param_warrior_xp;
                unset($stmt, $stmt2, $stmt3);
                $this->closeConn();
                return $level_up;
            }
            else {
                return $level_up;
            }
        }
        public function transfer($warriors) {
            /*$query_array = $warriors;
            $query_array[] = $this->username;*/
            $query_array = explode(",", $warriors);
            $query_array[] = $this->username;
            $in  = str_repeat('?,', count($query_array) - 2) . '?';
            $sql = "SELECT location FROM warriors WHERE warrior_id IN ($in) AND username= ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            if($stmt->rowCount() < count($query_array) - 1) {
                $this->gameMessage("ERROR: Some of the selected warriors does not exists!", true);
                return false;
            }
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $status = true;
            foreach($row as $key) {
                if($key['location'] != $this->session['location']) {
                    $status = false;
                    break;
                }
            }
            if($status === false) {
                $this->gameMessage("ERROR: One or more of your warriors are in the wrong city", true);
                return false;
            }
            
            switch($this->session['location']) {
                case 'tasnobil':
                    $city = 'cruendo';
                    break;
                case 'cruendo':
                    $city = 'tasnobil';
                    break;
            }
            array_unshift($query_array, $city);
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE warriors SET location= ? WHERE warrior_id IN ($in) AND username = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($query_array);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->getData($js = true);
        }
    }
?>