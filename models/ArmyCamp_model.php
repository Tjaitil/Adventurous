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
            $sql = "SELECT a.warrior_id, a.type, a.mission, a.fetch_report, a.health, a.rest, a.rest_start,
                    b.stamina_level, b.stamina_xp, b.technique_level, b.technique_xp,
                    b.precision_level, b.precision_xp, b.strength_level, b.strength_xp
                    FROM warriors AS a INNER JOIN warrior_levels AS b ON b.username = a.username AND b.warrior_id = a.warrior_id
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
            //Check if xp of one or more of the levels for each warrior is above next_level
            foreach($warrior_levels as $key) {
                for($i = 0; $i < count($skills); $i++) {
                    if($level_info[$key[$skills[$i] . '_level']] <= $key[$skills[$i].'_xp']) {
                        if($i === 0 ) {
                            $level_up[$key['warrior_id']]['update'] = "UPDATE warrior_levels SET";
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
            //Ajax function
            //Transfer 
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
        public function healWarrior($type, $warriors, $item = false, $amount = false) {
            $query_array = explode(",", $warriors);
            $in  = str_repeat('?,', count($query_array) - 1) . '?';
            $query_array[] = $this->username;
            $query_array[] = $this->session['location'];
            $sql = "SELECT warrior_id, health FROM warriors
            WHERE fetch_report=0 AND mission=0 AND warrior_id IN ($in) AND username=? AND location=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: One or more of your warriors does not exists!", true);
                return false;
            }
            
            if($type =='item') {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['health'] == 100) {
                    $this->gameMessage("ERROR: Warrior does not need to rest", true);
                    return false;
                }
            }
            else {
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(array_search("100", array_column($row, "health")) !== false) {
                    foreach($row as $key) {
                        if($key['health'] == 100) {
                            $this->gameMessage("ERROR: Warrior {$key['warrior_id']} don't need to rest", true);
                        }
                    }
                    return false;
                }
            }
            if(count(explode(",", $warriors)) > count($row)) {
                $this->gameMessage("ERROR: One or more of your warriors are on mission or training", true);
                return false;
            }
            if($type == 'item') {
                $item_amount = get_item($this->session['inventory'], $item)['amount'];
                if(!$item_amount > 0) {
                    $this->gameMessage("ERROR: You don't have any of this item", true);
                    return false;
                }
                else if($item_amount < $amount) {
                    $this->gameMessage("ERROR: You don't have enough of this item", true);
                    return false;
                }
                $healing = array();
                $healing['yest-herb'] = array("heal" => 25);
                $healing['healing potion'] = array("heal" => 35);
                $healing['yas-herb'] = array("heal" => 45);
            }
            else {
                $rest_start = date("Y-m-d H:i:s"); 
            }
            try {
                $this->conn->beginTransaction();
                if($type == 'item') {
                    $sql = "UPDATE warriors SET health=:health WHERE warrior_id=:warrior_id AND username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":health", $param_health, PDO::PARAM_STR);
                    $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_health = $row['health'] + ($healing[$item]['heal'] * $amount);
                    $param_warrior_id = $warriors;
                    $param_username = $this->username;
                    $stmt->execute();
                    
                    update_inventory($this->conn, $this->username, $item, - $amount);
                }
                else {
                    array_unshift($query_array, $rest_start);
                    $sql = "UPDATE warriors SET rest=1, rest_start=? WHERE warrior_id IN ($in) AND username=?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute($query_array);
                }
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            if($type == 'item') {
                $healing_amount = $healing[$item]['heal'] * $amount;
                $this->gameMessage("Warrior healed for {$healing_amount}, new health: {$param_health}", true);
            }
            else {
                $this->gameMessage("Warrior(s) on rest!", true);
            }
        }
        public function offRest($warriors) {
            $query_array = explode(",", $warriors);
            $in  = str_repeat('?,', count($query_array) - 1) . '?';
            $query_array[] = $this->username;
            $query_array[] = $this->session['location'];
            $sql = "SELECT warrior_id, health, rest_start FROM warriors WHERE rest=1 AND warrior_id IN ($in) AND username=? AND location=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: One or more of your warriors does not exists!", true);
                return false;
            }
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count(explode(",", $warriors)) > count($row)) {
                $this->gameMessage("ERROR: One or more of your warriors are on mission or training", true);
                return false;
            }
            if(array_search('0', array_column($row, '0')) !== false) {
                $this->gameMessage("ERROR: One or more of your warriors isn't resting", true);
                return false;
            }
            
            $warrior_data = array();
            
            foreach($row as $key => $value) {
                $rest_start = date_timestamp_get(new DateTime($value['rest_start']));
                $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
                $health_gained = (($date_now - $rest_start) / 60) * 3;
                if($health_gained + $value['health'] > 100) {
                    $warrior_data[$key]['health'] = 100;
                }
                else {
                    $warrior_data[$key]['health'] = $warrior_data[$key]['health'] + $health_gained;
                }
                $warrior_data[$key]['warrior_id'] = $value['warrior_id'];
            }
        
            try {
                $this->conn->beginTransaction();
                
                foreach($warrior_data as $key) {
                    $sql = "UPDATE warriors SET rest=0, health=? WHERE warrior_id=? AND username=?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute(array($key['health'], $key['warrior_id'], $this->username));
                }
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
                $this->gameMessage("Warriors off rest!", true);
        }
        public function changeType($warrior_id) {
            $sql = "SELECT type FROM warriors WHERE warrior_id=:warrior_id AND location=:location AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The warrior does not exists!", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $type = ($row['type'] == 'melee') ? 'ranged' : 'melee';
            $prices = array("ranged" => 600, "melee" => 500);
            
            if($prices[$type] > $this->session['gold']) {
                $this->gameMessage("ERROR: Not enough gold in inventory", true);
                return false;
            }
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE warriors SET type=:type WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_type = $type;
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                
                update_inventory($this->conn, $this->username, 'gold', -$prices[$type]);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("Type changed to {$type} for {$prices[$type]} gold", true);
        }
    }
?>