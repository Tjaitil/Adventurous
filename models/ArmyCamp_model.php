<?php
    class ArmyCamp_model extends model /*implements getDataInterface*/ {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, true);
        }
        public function getData($GET = false) {
            $data = array();
            if($GET === false) {
                $sql = "SELECT a.warrior_id, a.type, a.location, a.mission, a.fetch_report, a.health, a.rest, a.rest_start,
                        b.stamina_level, b.stamina_xp, b.technique_level, b.technique_xp,
                        b.precision_level, b.precision_xp, b.strength_level, b.strength_xp
                        FROM warriors AS a INNER JOIN warriors_levels AS b ON b.username = a.username AND b.warrior_id = a.warrior_id
                        WHERE a.username=:username GROUP BY a.warrior_id;";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                /*$stmt->bindParam(":location", $param_location, PDO::PARAM_STR);*/
                $param_username = $this->username;
                /*$param_location = $this->session['location'];*/
                $stmt->execute();
            }
            else {
                $queryArray = array();
                $queryArray = explode(",", $GET['warriors']);
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                
                $sql = "SELECT a.warrior_id, a.type, a.location, a.mission, a.fetch_report, a.health, a.rest, a.rest_start,
                        b.stamina_level, b.stamina_xp, b.technique_level, b.technique_xp,
                        b.precision_level, b.precision_xp, b.strength_level, b.strength_xp
                        FROM warriors AS a INNER JOIN warriors_levels AS b ON b.username = a.username AND b.warrior_id = a.warrior_id
                        WHERE a.warrior_id IN ($in) AND a.username= ? GROUP BY a.warrior_id;";
                        
                        // a.location= ? AND
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($queryArray);
            }
            $data['warrior_data'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $levels = array_unique(array_merge(
                                array_column($data['warrior_data'], 'stamina_level'),
                                array_column($data['warrior_data'], 'technique_level'),
                                array_column($data['warrior_data'], 'precision_level'),
                                array_column($data['warrior_data'], 'strength_level')));
            $in  = str_repeat('?,', count($levels) - 1) . '?';
            $sql = "SELECT skill_level, next_level FROM warriors_level_data WHERE skill_level IN ($in)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute(array_values($levels));
            $data['levels_data'] = array();
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['levels_data'][$row['skill_level']] = $row['next_level'];
            }
            if($GET !== false) {
                get_template('warriors_levels', array($data['warrior_data'], $data['levels_data']), true);
            }
            else {
                return $data;
            }
        }
        public function getCountdown($GET) {
            // Function to echo date for ajax request
            if(explode(",", $GET['warriors'])[0] === "") {
                $sql = "SELECT training_countdown, fetch_report FROM warriors WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                /*$stmt->bindParam(":location", $param_location, PDO::PARAM_STR);*/
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();   
            }
            else {
                $queryArray = array();
                $queryArray = explode(",", $GET['warriors']);
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                
                $sql = "SELECT training_countdown, fetch_report FROM warriors WHERE warrior_id IN ($in) AND username=?";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($queryArray);
            }
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            $data = array();
            foreach($row as $key) {
                $datetime = new DateTime($key['training_countdown']);
                $date = date_timestamp_get($datetime);
                echo $date . "|" . $key['fetch_report'] . "||";
            }
        }
        public function checkWarriorLevel($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to transfer warriors between Tasnobil and Cruendo
            
            $sql = "SELECT warrior_id, stamina_level, stamina_xp, technique_level, technique_xp, precision_level, precision_xp,
                    strength_level, strength_xp FROM warriors_levels
                        WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = explode(",", $POST['warriors'])[0];
            $param_username = $this->username;
            $stmt->execute();
            // If rowcount is less than 1 then the warrior id may have been changed
            if($stmt->rowCount() < 1) {
                $this->gameMessage("ERROR: Warrior not found, please try again", true);
                return false;
            }
            
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $uniqe_levels = array_values(array_unique(array($row[0]['stamina_level'],
                                                            $row[0]['technique_level'],
                                                            $row[0]['precision_level'],
                                                            $row[0]['strength_level'])));
            // $in makes a prepared statement string by replacing the value with a ? to get multiple values;
            $in  = str_repeat('?,', count($uniqe_levels) - 1) . '?';
            $sql = "SELECT skill_level, next_level FROM warriors_level_data WHERE skill_level IN ($in)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($uniqe_levels);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $level_info = array();
            foreach($row2 as $key) {
                $level_info[$key['skill_level']] = $key['next_level'];
            }
            
            $statements = array();
            $skills = array('stamina', 'technique', 'precision', 'strength');
            $level_up = array();
            //Check if xp of one or more of the levels for each warrior is above next_level
            foreach($warriors_leves = $row as $key) {
                for($i = 0; $i < count($skills); $i++) {
                    if($level_info[$key[$skills[$i] . '_level']] <= $key[$skills[$i].'_xp']) {
                        if($i === 0 ) {
                            $level_up[$key['warrior_id']]['update'] = "UPDATE warriors_levels SET ";
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
                    $this->db->conn->beginTransaction();
                    
                    for($i = 0; $i < count($statements); $i++ ) {
                        $stmt = $this->db->conn->prepare($statements[$i]);
                        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                        $param_username = $this->username;
                        $stmt->execute();
                    }
    
                    if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                        $this->UpdateGamedata->updateXP('warrior', 20 * count($level_up));
                    }                
                    $this->db->conn->commit();
                }
                catch(Exception $e) {
                    $this->errorHandler->catchAJAX($this->db, $e);
                    return false;
                }
                unset($stmt, $stmt2, $stmt3);
                /* Echo order, split by "|"
                * [0] -> possibly level up message;
                * [1] -> gameMessage
                */
               echo "|";
               $this->gameMessage("Warrior {$param_warrior_id} leveled up", true);
            }
            else {
                $this->gameMessage("ERROR: None of your warriors is leveling up", true);
                return false;
            }
            $this->db->closeConn();
        }
        public function transfer($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to transfer warriors between Tasnobil and Cruendo
            $query_array = explode(",", $POST['warriors']);
            $warrior_amount = count($query_array);
            $query_array[] = $this->username;
            $in  = str_repeat('?,', count($query_array) - 2) . '?';
            $sql = "SELECT location FROM warriors WHERE warrior_id IN ($in) AND username= ?";
            $stmt = $this->db->conn->prepare($sql);
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
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warriors SET location= ? WHERE warrior_id IN ($in) AND username = ?";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($query_array);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("{$warrior_amount} transferred to {$city}", true);
        }
        public function healWarrior($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to heal warrior with either item or set warrior on rest
            $type = $POST['type'];
            $warriors = $POST['warriors'];
            $item = $POST['item'];
            $amount = $POST['amount'];
            
            $query_array = explode(",", $warriors);
            $in  = str_repeat('?,', count($query_array) - 1) . '?';
            $query_array[] = $this->username;
            $query_array[] = $this->session['location'];
            $sql = "SELECT warrior_id, health FROM warriors
            WHERE fetch_report=0 AND mission=0 AND warrior_id IN ($in) AND username=? AND location=?";
            $stmt = $this->db->conn->prepare($sql);
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
                $this->db->conn->beginTransaction();
                if($type == 'item') {
                    $sql = "UPDATE warriors SET health=:health WHERE warrior_id=:warrior_id AND username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":health", $param_health, PDO::PARAM_STR);
                    $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_health = $row['health'] + ($healing[$item]['heal'] * $amount);
                    $param_warrior_id = $warriors;
                    $param_username = $this->username;
                    $stmt->execute();
                    
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, - $amount, true);
            
                }
                else {
                    array_unshift($query_array, $rest_start);
                    $sql = "UPDATE warriors SET rest=1, rest_start=? WHERE warrior_id IN ($in) AND username=?";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->execute($query_array);
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            if($type == 'item') {
                $healing_amount = $healing[$item]['heal'] * $amount;
                $this->gameMessage("Warrior healed for {$healing_amount}, new health: {$param_health}", true);
            }
            else {
                $this->gameMessage("Warrior(s) on rest!", true);
            }
            $this->db->closeConn();
        }
        public function offRest($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to get warrior(s) off rest and available for other actions
            $query_array = explode(",", $POST['warriors']);
            $in  = str_repeat('?,', count($query_array) - 1) . '?';
            $query_array[] = $this->username;
            $query_array[] = $this->session['location'];
            $sql = "SELECT warrior_id, health, rest_start FROM warriors WHERE rest=1 AND warrior_id IN ($in) AND username=? AND location=?";
            $stmt = $this->db->conn->prepare($sql);
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
                $this->db->conn->beginTransaction();
                
                foreach($warrior_data as $key) {
                    $sql = "UPDATE warriors SET rest=0, health=? WHERE warrior_id=? AND username=?";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->execute(array($key['health'], $key['warrior_id'], $this->username));
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("");
        }
        public function changeType($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to get warrior(s) off rest and available for other actions
            $warrior_id = $POST['warriors'];
            $sql = "SELECT type FROM warriors WHERE warrior_id=:warrior_id AND location=:location AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
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
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warriors SET type=:type WHERE warrior_id=:warrior_id AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_type = $type;
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', -$prices[$type], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->gameMessage("Type changed to {$type} for {$prices[$type]} gold", true);
        }
    }
?>