<?php
    class UpdateAdventure_model extends model {
        public $username;
        public $session;
        public $adventure_data;
        public $daqloon_status = array();
        public $warrior_status = array();
        public $warriors;
        public $daqloons;
        public $battle_progress = array();
        public $warrior_damage = array();
        public $daqloon_damage = array();
        public $combo_attack = array('warrior' => 0, 'daqloon' => 0);
        public $battle_statistics = array();
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, true);
        }
        public function updateAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
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
            $stmt = $this->db->conn->prepare($sql);
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
            
            if($this->adventure_data['battle_result'] == 0) {
                $check = $this->getResult();
                if($check === false) {
                    $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                }
            }
            else {
                $sql = "SELECT daqloon_damage, warrior_damage, warrior_wounded, daqloon_wounded, warrior_combo, daqloon_combo
                        FROM adventure_battles WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $param_adventure_id;
                $stmt->execute();
                $this->battle_statistics = $stmt->fetch(PDO::FETCH_ASSOC);
                if($this->adventure_data['battle_result'] == 1) {
                    $this->battle_statistics['result'] = "Warriors wins!";
                }
                else {
                    $this->battle_statistics['result'] = "Daqloons wins!";   
                }
            }

            $role = array_search($this->username, $this->adventure_data);
            if($role === false) {
                return false;
            }
            else {
                $this->updateRole($role);
            }
        }
        public function updateRole($role) {
            $sql = "SELECT battle_result FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $stmt->execute();
            $battle_result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT user_xp, warrior_xp_min, warrior_xp_max FROM adventures_data WHERE location=:location AND difficulty=:difficulty";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt2->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $param_location = $this->adventure_data['location'];
            $param_difficulty = $this->adventure_data['difficulty'];
            $stmt2->execute();
            $xp_data = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $crystal_chance = $this->calculateCrystal();
            
            switch($this->adventure_data['difficulty']) {
                case 'easy':
                    $count = 2;
                    break;
                
                case 'medium':
                    $count = 3;
                    break;
                
                case 'hard':
                    $count = 5;
                    break;
            }
            $rewards = array();
            $warrior_xp = array();
            switch($role) {
                case 'farmer':
                case 'miner':
                case 'trader':
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty
                            ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $stmt->execute();
                    $rewards[] = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $sql2 = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty <= :difficulty AND item !=:name
                            ORDER BY RAND() LIMIT {$count}";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt2->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt2->bindParam(":name", $param_name, PDO::PARAM_STR);
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_name = $rewards[0]['item'];
                    $stmt2->execute();
                    while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $rewards[] = $row;
                    }
                    break;
                
                case 'warrior':
                    $count -= 1;
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty
                            ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $stmt->execute();
                    $rewards[] = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty <= :difficulty AND item != :name
                            ORDER BY RAND() LIMIT {$count}";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt->bindParam(":name", $param_name, PDO::PARAM_STR); 
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_name = $rewards[0]['item'];
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $rewards[] = $row;
                    }
                    
                    $sql = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp, warrior_id, username FROM warrior_levels
                            WHERE warrior_id IN (SELECT warrior_id FROM warriors WHERE mission=1 AND username=:username)
                                  AND username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $warrior_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $warrior_xp;
                    for($i = 0; $i < count($warrior_levels); $i++) {
                        $warrior_xp[$i]['warrior_id'] = $warrior_levels[$i]['warrior_id'];
                        $warrior_xp[$i]['stamina_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$i]['stamina_xp'] += $warrior_xp[$i]['stamina_xp'];
                        $warrior_xp[$i]['technique_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$i]['technique_xp'] += $warrior_xp[$i]['technique_xp'];
                        $warrior_xp[$i]['precision_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$i]['precision_xp'] += $warrior_xp[$i]['precision_xp'];
                        $warrior_xp[$i]['strength_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warrior_levels[$i]['strength_xp'] += $warrior_xp[$i]['strength_xp'];
                    }
            }
            $roles = array("farmer", "miner", "trader", "warrior");
            unset($roles[$role]);
            
            $none = 0;
            for($i = 0; $i < count($roles); $i++) {
                if($this->adventure_data[$roles[$i]] ==  'none') {
                    $none++;    
                }
            }
            for($i = 0; $i < count($rewards); $i++) {
                $rewards[$i]['amount'] = round(rand($rewards[$i]['min_amount'], $rewards[$i]['max_amount']));
                if($battle_result != 1) {
                    $rewards[$i]['amount'] = round($rewards[$i]['amount'] / 2);
                }
                unset($rewards[$i]['min_amount']);
                unset($rewards[$i]['max_amount']);
            }
        
            try {
                $this->db->conn->beginTransaction();
                /*
                foreach($rewards as $key) {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($key['item'], $key['amount'], true);
                }
                
                if($crystal_chance ===x 1) {
                    $rewards[] = array("item" => $this->adventure_data['location'] . 'crystal', "amount" => 1);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($this->adventure_data['location'] . 'crystal', 1, true);
                }
                
                // Update xp
                $this->UpdateGamedata->updateXP($role, $xp_data['user_xp']);
                
                $sql = "UPDATE adventure SET adventure_id=0, adventure_status=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();

                if(count($roles) - $none == 0) {
                    $sql = "DELETE FROM adventures WHERE adventure_id=adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                    $param_adventure_id = $this->adventure_data['adventure_id'];
                    $stmt->execute();
                }
                else {
                    $sql = "UPDATE adventures SET {$role}='none' WHERE adventure_id=:adventure_id AND {$role}=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_adventure_id = $this->adventure_data['adventure_id'];
                    $param_username = $this->username;
                    $stmt->execute();
                }
                
                if($role === 'warrior') {
                    $sql = "UPDATE warrior_levels SET stamina_xp=?, technique_xp=?, precision_xp=?, strength_xp=?
                            WHERE warrior_id=? AND username=?";
                    $stmt = $this->db->conn->prepare($sql);
                    foreach($warrior_levels as $key) {
                        $stmt->execute(array_values($key));
                    }
                    $sql2 = "UPDATE warriors SET mission=0 WHERE warrior_id= ? AND username = ?";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    foreach($warrior_levels as $key) {
                        $stmt2->execute($key['warrior_id'], $key['username']);
                    }
                }*/
        
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->adventure_data['user_xp'] = $xp_data['user_xp'];
            $this->adventure_data['role'] = $role;
            get_template('adventure_rewards', array('adventure_data' => $this->adventure_data,
                                                    'rewards' => $rewards,
                                                    'statistics' => $this->battle_statistics,
                                                    'warrior_xp' => $warrior_xp), true);
            
            $this->db->closeConn();
        }
        public function getResult() {
            //Get levels of the soldiers, armour and weapons
            $sql = "SELECT warrior_id FROM warriors WHERE mission=1 AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
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
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $warrior_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT stamina_level, technique_level, precision_level, strength_level FROM warrior_levels
                    WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $warrior_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT attack, defence FROM warrior_armory WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->warriors = array();
            for($q = 0; $q < count($warrior_data); $q++) {
                $this->warriors[$q] = array_merge($warrior_data[$q], $warrior_levels[$q], $stats[$q]);
            }
            
            //Get the strength of daqloon
            $sql = "SELECT attack, defence, amount FROM daqloon_stats WHERE difficulty=:difficulty";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $param_difficulty = $this->adventure_data['difficulty'];
            $stmt->execute();
            $daqloon = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->daqloons = array();
            $daqloon_amount = 2;
            for($i = 0; $i < $daqloon_amount; $i++) {
                $this->daqloons[$i] = array("health" => rand(100,120), "id" => $i+1, "attack" => $daqloon['attack'] - rand(1,3),
                                      "defence" => $daqloon['defence'] - rand(1,3));
            }
            
            $total_amount = count($this->warriors);
            $total_daqloon = count($this->daqloons);
            $battle_result;
            $duration = 1;
            do {
                $this->battle_progress[] = "Hit: " . $duration;
                for($i = 0; $i < count($this->warriors); $i++) {
                    $warrior = $this->warriors[$i];
                    if(empty($this->daqloons[$i])) {
                        $this->daqloons = array_values($this->daqloons);
                        for($d = 0; $d < count($this->daqloons); $d++) {
                            if(!empty($this->daqloons[$d])) {
                                $daqloon = $this->daqloons[$d];
                                break;
                            }
                        }
                    }
                    else {
                        $daqloon = $this->daqloons[$i];
                    }
                    //Remove daqloon(s) who are wounded from $this->daqloons
                    if(array_key_exists('d_' . $daqloon['id'], $this->daqloon_status) == true) {
                        unset($this->daqloons[$i]);
                        $this->daqloons = array_values($this->daqloons);
                        if(!count($this->daqloons) > 0) {
                            $this->battle_result = 'Warriors wins';
                            break;
                        }
                        for($l = 0; $l < count($this->daqloons); $l++) {
                            if(!empty($this->daqloons[$l])) {
                                $daqloon = $this->daqloons[$l];
                            }
                        }
                    }
                    //Remove warrior(s) who are wounded from $this->arriors
                    if(array_key_exists('w_' . $this->warriors[$i]['warrior_id'], $this->warrior_status) == true) {
                        unset($this->warriors[$i]);
                        $this->warriors = array_values($this->warriors);
                        continue;
                    }
                    $first = rand(1,2);
                    if($duration % 3 !== 0 && $this->warriors[$i]['type'] === 'ranged') {
                        $this->daqloons[$i] = $this->warriorHit($daqloon, $warrior, $duration);   
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
                    if($daqloon_count != $warrior_count) {
                        ($daqloon_count > $warrior_count) ? $array = "daqloons" : $array = "warriors";
                        switch($array) {
                            case "daqloons":
                                $count = round(($daqloon_count - $warrior_count) / $warrior_count);
                                $x = max(array_keys($this->daqloons));
                                for ($w = 0; $w < $count; $w++) {
                                    if($duration % 3 === 0 && $this->warriors[$i]['type'] === 'ranged') {
                                        $this->warriors[$i] = $this->daqloonHit($this->daqloons[$x - $w - ($i * $count)],
                                                                                $this->warriors[$i]);
                                    }
                                    else if($this->warriors[$i]['type'] != 'ranged') {
                                        $this->warriors[$i] = $this->daqloonHit($this->daqloons[$x - $w - ($i * $count)],
                                                                                $this->warriors[$i]);
                                    }
                                    if($this->warriors[$i]['health'] < 10.1) {
                                        break;
                                    }
                                }
                                break;
                            case "warriors":
                                $count = round(($warrior_count - $daqloon_count) / $daqloon_count);
                                $x = max(array_keys($this->warriors));
                                for ($w = 0; $w < $count; $w++) {
                                    $this->daqloons[$i] = $this->warriorHit($this->daqloons[$i], $this->warriors[$x - $w], $i);
                                    if($this->daqloons[$i]['health'] < 10) {
                                        break;
                                    }
                                }
                                break;
                        }
                    }
                }
                if(count($this->warrior_status) === $total_amount) {
                        $this->battle_result = "Daqloons wins";
                        $battle_result = 2;
                        $this->battle_progress[] = "Daqloons wins";
                        break;
                    }
                else if(count($this->daqloon_status) === $total_daqloon) {
                        $this->battle_result = 'Warriors wins';
                        $battle_result = 1;
                        $this->battle_progress[] = "Warriors wins";
                        break;
                }
                $this->battle_progress[] = "</br>";
                $duration++;
                if($duration == 100) {
                    $battle_result = 1;
                    $this->battle_progress[] = "Battle went on too long, daqloons fled";
                    break;
                }
            }
            while(empty($battle_result));
            
            for($i = 0; $i < count($this->warriors); $i++) {
                $update_data[$i] = array($this->warriors[$i]['health'], $this->warriors[$i]['warrior_id'], $this->username);
            }
            $this->getStatistics();
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE adventures SET battle_result=:battle_result WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":battle_result", $param_battle_result, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_battle_result = $battle_result;
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $stmt->execute();
                
                $sql2 = "UPDATE warriors SET health = ? WHERE warrior_id = ? AND username = ?";
                $stmt2 = $this->db->conn->prepare($sql2);
                foreach($update_data as $key) {
                    $stmt2->execute($key);
                }
                
                $sql3 = "INSERT INTO adventure_battle
                        (adventure_id, daqloon_damage, warrior_damage, warrior_wounded, daqloon_wounded, warrior_combo, daqloon_combo)
                        VALUES(:adventure_id, :daqloon_damage, :warrior_damage, :daqloon_wounded, :warrior_wounded, :daqloon_combo,
                        :warrior_combo)";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt3->bindParam(":daqloon_damage", $param_daqloon_damage, PDO::PARAM_INT);
                $stmt3->bindParam(":warrior_damage", $param_warrior_damage, PDO::PARAM_INT);
                $stmt3->bindParam(":daqloon_wounded", $param_daqloon_wounded, PDO::PARAM_INT);
                $stmt3->bindParam(":warrior_wounded", $param_warrior_wounded, PDO::PARAM_INT);
                $stmt3->bindParam(":daqloon_combo", $param_daqloon_combo, PDO::PARAM_INT);
                $stmt3->bindParam(":warrior_combo", $param_warrior_combo, PDO::PARAM_INT);
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_daqloon_damage = $this->battle_statistics['daqloon_damage'];
                $param_warrior_damage = $this->battle_statistics['warrior_damage'];
                $param_daqloon_wounded = $this->battle_statistics['daqloon_wounded'];
                $param_warrior_wounded = $this->battle_statistics['warrior_wounded'];
                $param_daqloon_combo = $this->battle_statistics['daqloon_combo'];
                $param_warrior_combo = $this->battle_statistics['warrior_combo'];
                $stmt3->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
        }
        protected function daqloonHit($daqloon, $warrior) {
                $daqloon_hit = $daqloon['attack'] + rand(1,3) * (0.025 * $warrior['defence']);
                $warrior['health'] -= $daqloon_hit;
                $this->daqloon_damage[] = $daqloon_hit;
                $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " got hit for " . $daqloon_hit . " by daqloon " .
                $daqloon['id'] . ", warrior health: " . $warrior['health'];
                if($warrior['health'] < 10) {
                    $this->warrior_status['w_' . $warrior['warrior_id']] = "wounded";
                    $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " wounded";
                    $warrior['health'] = 10;
                }
                return $warrior;
        } 
        protected function warriorHit($daqloon, $warrior, $duration) {
                $warrior_hit = $warrior['attack'] + (0.5 * $warrior['strength_level']) + 
                               (($warrior['attack'] / 100) * $warrior['technique_level'] + rand(1,3)) //Damage increase from technique 
                             * (0.025 * $daqloon['defence']);
                             - ($warrior['stamina_level'] - ($duration * 0.2)); //Hit damage decrease when they are fighting
                $daqloon['health'] -= $warrior_hit;
                $this->warrior_damage[] = $warrior_hit;
                $this->battle_progress[] = "Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by warrior " .
                $warrior['warrior_id'] . ", daqloon health: " . $daqloon['health'];
                //precision_level is hit percentage for getting in a second attack
                if(rand(1,100) <= $warrior['precision_level']) {
                    $daqloon['health'] -= $warrior_hit;
                    $this->warrior_damage[] = $warrior_hit;
                    $this->combo_attack['warrior'] += 1;
                    $this->battle_progress[] = "COMBO! Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by " .
                    $warrior['warrior_id'] . " Health: " . $daqloon['health'];
                }

                if($daqloon['health'] < 10 ) {
                    $this->daqloon_status['d_' . $daqloon['id']] = "wounded";
                    $this->battle_progress[] =  'Daqloon ' . $daqloon['id'] . ' wounded';
                }
                return $daqloon;
        } 
        protected function calculateCrystal() {
            $sql = "SELECT min_chance, max_chance FROM adventure_crystals WHERE difficulty=:difficulty AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_difficulty = $this->adventure_data['difficulty'];
            $param_location = $this->adventure_data['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $rand = rand($row['min_chance'], $row['max_chance']);
            if($rand === 1) {
                return 1;
            }
            else if($rand > 1) {
                return 0;
            }
        }
        public function getStatistics() {
            $this->battle_statistics = array('battle_progress' => $this->battle_progress, 'result'  => $this->battle_result,
                          'daqloon_damage' => array_sum($this->daqloon_damage), 'warrior_damage' => array_sum($this->warrior_damage),
                          'warrior_wounded' => count($this->warrior_status), 'daqloon_wounded' => count($this->daqloon_status),
                          'warrior_combo' => $this->combo_attack['warrior'], 'daqloon_combo' => $this->combo_attack['daqloon']);
        }
    }
?>