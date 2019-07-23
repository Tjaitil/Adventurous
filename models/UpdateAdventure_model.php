<?php
    class UpdateAdventure_model extends model {
        public $username;
        public $session;
        public $daqloon_status = array();
        public $warrior_status = array();
        public $adventure_data;
        public $warriors;
        public $daqloons;
        public $warrior_hits = array();
        public $daqloon_hits = array();
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function updateAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $adventure_id = $row['adventure_id'];
            
            if(!$adventure_id > 0) {
                $this->gameMessage("You don't have any adventures!", true);
                return false;
            }
            
            $sql = "SELECT difficulty, location, farmer, miner, trader, warrior, adventure_status, adventure_countdown,
                    battle_result FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $stmt->execute();
            $this->adventure_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $time = date("Y-m-d H:i:s");
            if($time < $this->adventure_data['adventure_countdown']) {
                $this->gameMessage("The adventure is not yet finished!", true);
                return false;
            }
            $this->adventure_data['adventure_id'] = $adventure_id;
            
            if(!$this->adventure_data['battle_result'] == null ) {
                $this->getResult();
            }

            $role = array_search($this->username, $this->adventure_data);
            if($role === false) {
                return false;
            }
            $this->updateRole($role);
            
            /*if($this->username === $this->adventure_data['adventure_leader']) {
                
                try {
                    $this->conn->beginTransaction();
                    
                    $sql = "DELETE FROM adventures WHERE adventure_id=:adventure_id AND adventure_leader=:adventure_leader";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":advneture_id", $param_adventure_id, PDO::PARAM_STR);
                    $stmt->bindParam(":adventure_leader", $param_username, PDO::PARAM_STR);
                    $param_adventure_id = $this->adventure_data['adventure_id'];
                    $param_username = $this->username;
                    $stmt->execute();
                    
                    $this->conn->commit();
                }
                catch(Exception $e) {
                    $this->conn->rollBack();
                    print $e->getMessage();
                    return false;
                }
            }*/
        }
        
        public function updateRole($role) {
            $sql = "SELECT battle_result FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $stmt->execute();
            $battle_result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT user_xp, warrior_xp_min, warrior_xp_max FROM adventures_data WHERE location=:location AND difficulty=:difficulty";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt2->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $param_location = $this->adventure_data['location'];
            $param_difficulty = $this->adventure_data['difficulty'];
            $stmt2->execute();
            $xp_data = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            switch($this->adventure_data['difficulty']) {
                case 'easy':
                    $count = 1;
                    break;
                
                case 'medium':
                    $count = 2;
                    break;
                
                case 'hard':
                    $count = 4;
                    break;
            }
            switch($role) {
                case 'farmer':
                case 'miner':
                case 'trader':
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty AND location=:location
                            ORDER BY RAND() LIMIT {$count}";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_location = $this->adventure_data['location'];
                    $stmt->execute();
                    $rewards = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                    foreach($rewards as $key) {
                        $key['amount'] = rand($key['min_amount'], $key['max_amount']);
                        if($battle_result != 1) {
                            $key['amount'] /= 2;
                        }
                    }
                    break;
                case 'warrior':
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty AND location=:location";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_location = $this->adventure_data['location'];
                    $stmt->execute();
                    $rewards = $stmt->fetch(PDO::FETCH_ASSOC);
                    $rewards['amount'] = rand($rewards['min_amount'], $rewards['max_amount']);;
                    if($battle_result != 1) {
                        $rewards['amount'] /= 2;
                    }
                    /*$sql = "SELECT warrior_id FROM warriors WHERE mission=1 AND username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $warriors = array();
                    foreach($row as $key) {
                        array_push($warriors, $key['warrior_id']);
                    }
                    $warriors[] = $this->username;

                    $in  = str_repeat('?,', count($warriors) - 2) . '?';*/
                    $sql = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp, warrior_id, username FROM warrior_levels
                            WHERE warrior_id IN (SELECT warrior_id FROM warriors WHERE mission=1 AND username=:username) AND username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $warrior_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    
                    foreach($warrior_levels as $key => $value) {
                        $warrior_levels[$key]['stamina_xp'] =
                        intval($key['stamina_xp']) + rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$key]['technique_xp'] =
                        intval($key['technique_xp']) + rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$key]['precision_xp'] =
                        intval($key['precision_xp']) + rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$key]['strength_xp'] =
                        intval($key['strength_xp']) + rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                    }
            }
            try {
                $this->conn->beginTransaction();
             
                $sql = "UPDATE adventures_$role SET username='none' WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_username = $this->username;
                $stmt->execute();
                
                if($role !== 'warrior') {
                    foreach($rewards as $key) {
                        update_stockpile($this->conn, $this->username, $key['item'], $key['amount']);
                    }
                }
                else if ($role === 'warrior') {
                    update_stockpile($this->conn, $this->username, $rewards['item'], $rewards['amount']);
                }
                
                update_xp($this->conn, $this->username, $role, $xp_data['user_xp']);
                
                $sql = "UPDATE adventure SET adventure_id=0, adventure_status=0 WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE adventures SET $role='none' WHERE adventure_id=:adventure_id AND $role=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_username = $this->username;
                $stmt->execute();
                
                if($role === 'warrior') {
                    $sql = "UPDATE warrior_levels SET stamina_xp=?, technique_xp=?, precision_xp=?, strength_xp=?
                            WHERE warrior_id=? AND username=?";
                    $stmt = $this->conn->prepare($sql);
                    foreach($warrior_levels as $key) {
                        $stmt->execute(array_values($key));
                    }
                }
        
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->gameMessage("You have completed an adventure!", true); 
            $this->closeConn();
        }
        
        public function getResult() {
            //Get levels of the soldiers, armour and weapons
            $sql = "SELECT warrior_id FROM warriors WHERE mission=2 AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->adventure_data['warrior'];
            $stmt->execute();
            $warriors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $query_array = array();
            foreach($warriors as $key) {
                array_push($query_array, $key['warrior_id']);
            }
            $query_array[] = $this->adventure_data['warrior'];
            $in  = str_repeat('?,', count($query_array) - 2) . '?';
            
            $sql = "SELECT warrior_id, type, health FROM warriors WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            $warrior_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT stamina_level, technique_level, precision_level, strength_level FROM warrior_levels
                    WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            $warrior_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT attack, defence FROM warrior_armory WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($query_array);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->warriors = array();
            for($q = 0; $q < count($warrior_data); $q++) {
                $this->warriors[$q] = array_merge($warrior_data[$q], $warrior_levels[$q], $stats[$q]);
            }
            
            //Get the strength of daqloon
            $sql = "SELECT stats, amount FROM daqloon_stats WHERE difficulty=:difficulty AND location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_difficulty = $this->adventure_data['difficulty'];
            $param_location = $this->adventure_data['location'];
            $stmt->execute();
            $daqloon = $stmt->fetch(PDO::FETCH_ASSOC);
            $split = explode("+", $daqloon['stats']);
            $daqloon['attack'] = $split[0];
            $daqloon['defence'] = $split[1];
            $this->daqloons = array();
            $daqloon_amount = 2;
            for($i = 0; $i < $daqloon_amount; $i++) {
                $this->daqloons[$i] = array("health" => rand(100,120), "id" => $i+1, "attack" => $daqloon['attack'] - rand(1,3),
                                      "defence" => $daqloon['defence'] - rand(1,3));
            }
            $battle_result;
            $duration = 0;
            do {
                print "</br>";
                for($i = 0; $i < count($this->warriors); $i++) {
                    $daqloon = $this->daqloons[$i];
                    $warrior = $this->warriors[$i];
                    //Remove warriors who are wounded from $warrior
                    if(array_key_exists($this->warriors[$i]['warrior_id'], $this->warrior_status) == true) {
                        unset($this->warriors[$i]);
                        $this->warriors = array_values($this->warriors);
                        var_dump($this->warriors);
                        print "continue" . "</br>";
                        continue;
                    }
                    $first = rand(1,2);
                    
                    
                    if($duration % 3 === 0 && $this->warriors['type'] === 'ranged') {
                        $this->warriors[$i] = $this->daqloonHit($daqloon, $warrior);   
                    }
                    else if($first === 1) {
                        $this->daqloons[$i] = $this->warriorHit($daqloon, $warrior, $duration);
                        $this->warriors[$i] = $this->daqloonHit($daqloon, $warrior);
                    }
                    else {
                        $this->warriors[$i] = $this->daqloonHit($daqloon, $warrior);
                        $this->daqloons[$i] = $this->warriorHit($daqloon, $warrior, $duration);
                    }
                    $daqloon_count = count($this->daqloons);
                    $warrior_count = count($this->warriors);
                    var_dump($daqloon_count);
                    var_dump($warrior_count);
                    if($daqloon_count != $warrior_count) {
                        ($daqloon_count > $warrior_count) ? $array = "daqloons" : $array = "warriors";
                        print "switch" . "</br>";
                        switch($array) {
                            case "daqloons":
                                $count = $daqloon_count - $warrior_count;
                                for ($w = 0; $w < $count; $w++) {
                                    $this->warriors[$i] = $this->daqloonHit($this->daqloons[$i], $this->warriors[$i]);
                                }
                                break;
                            case "warriors":
                                $count = $warrior_count - $daqloon_count;
                                for ($w = 0; $w < $count; $w++) {
                                    $this->daqloons[$i] = $this->warriorHit($this->daqloons[$i], $this->warrior[$i]);
                                }
                                break;
                        }
                    }
                }
                if(count($this->warrior_status) == count($warrior_data)) {
                    $battle_result = 0;
                    print "loss";
                    break;
                }
                else if(count($this->daqloon_status) == $daqloon_amount) {
                    $battle_result = 1;
                    print "wins";
                    break;
                }
                $duration++;
                if($duration == 20) {
                    print "break";
                    break;
                }
            }
            while(empty($battle_result));
            
            
            $update_data = array();
            
            for($i = 0; $i < count($this->warriors); $i++) {
                $update_data[$i] = array($this->warriors[$i]['health'], $this->warriors[$i]['warrior_id'], $this->username);
            }            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE adventures SET battle_result=:battle_result WHERE adventure_id=:adventure_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":battle_result", $param_battle_result, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_battle_result = $battle_result;
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $stmt->execute();
                
                $sql2 = "UPDATE warriors SET health = ? WHERE warrior_id = ? AND username = ?";
                $stmt2 = $this->conn->prepare($sql2);
                foreach($update_data as $key) {
                    $stmt2->execute($key);
                }
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            echo ($battle_result == 1) ? "Battle resulted in a win!" : "Battle resulted in a loss";
        }
        
        protected function daqloonHit($daqloon, $warrior) {
                $daqloon_hit = $daqloon['attack'] + rand(1,3) * (0.025 * $warrior['defence']);
                $warrior['health'] -= $daqloon_hit;
                $this->daqloonHit[] = "warrior " . $warrior['warrior_id'] . " got hit for " . $daqloon_hit .
                " warrior health: " . $warrior['health'];
                if($warrior['health'] < 15 ) {
                    $this->warrior_status[$warrior['warrior_id']] = "wounded";
                    $warrior['health'] = 10;
                }
                return $warrior;
        }
        
        protected function warriorHit($daqloon, $warrior, $duration) {
                $warrior_hit = $warrior['attack'] +
                               (($warrior['attack'] / 100) * $warrior['technique_level'] + rand(1,3)) //Damage increase from technique 
                             * (0.025 * $daqloon['defence'])
                             - ($duration / ($warrior['stamina_level'] * 0.2)); //Hit damage decrease when they are fighting
                $daqloon['health'] -= $warrior_hit;
                //precision_level is hit percentage for getting in a second attack
                if(rand(1,100) <= $warrior['precision_level']) {
                    $daqloon['health'] -= $warrior_hit;
                }
                $this->warriorHit[]= "daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " Health: " . $daqloon['health'];
                if($daqloon['health'] < 10 ) {
                    $this->daqloon_status[$daqloon['id']] = "wounded";
                }
                return $daqloon;
        }
        
        protected function calculateCrystal() {
            $sql = "SELECT chance FROM adventure_crystals WHERE difficulty=:difficulty AND location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_difficulty;
            $param_location;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $chance = explode("/",$row['chance']);
            $chance_min = $chance[0];
            $chance_max = $chance[0];
            
            $rand = rand($chance_min, $chance_max);
            if($rand === 1) {
                return 1;
            }
            else if($rand > 1) {
                return 0;
            }
        }
        
        /*protected function healthCheck($chain, $key, $i, $method) {
                switch($method) {
                    case "warriors":
                        $array = $this->warriors;
                        break;
                    case "daqloons":
                        $array = $this->daqloons;
                        break;
                }
                print "healthcheck!";
                if($key['health'] < 15) {
                    for($x = 0; $x < count($array); $x++) {
                        if($array[$x]['health'] > 10) {
                            $key = $array[$x];
                            break;
                        }
                        else {
                            "None found!";
                        }
                    }
                    unset($array[$i]);
                }
                return $key;
        }*/
    }
?>