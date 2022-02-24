<?php
    class combat_model extends model {
        public $username;
        public $session;
        protected $db;
        private $data;
        private $daqloon_status = array();
        private $warrior_status = array();
        private $warrior_army;
        private $warriors;
        private $warrior_amount;
        private $daqloons;
        private $battle_progress = array();
        private $warrior_damage = array();
        private $daqloon_damage = array();
        private $combo_attack = array('warrior' => 0, 'daqloon' => 0);
        private $blocked = array('warrior' => 0, 'daqloon' => 0);
        private $stats = array("blocked" => array("warrior" => 0, "daqloon" => 0), 
                        "missed" => array('warrior' => 0, 'daqloon' => 0),
                        "wounded" => array('warrior' => 0, 'daqloon' => 0));
        private $weapons = array(
            1 => array('sword', 'bow'),
            2 => array('knife', 'dagger')
        );
        private $duration;
        private $warrior_i;
        private $daqloon_i;
        private $POST;
        function __construct ($session, $db) {
            $this->username = $session['username'];
            $this->session = $session;
            $this->db = $db;
        }
        public function calculate($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to calculate the fight result based on the provided information
            // $this->data = json_decode($POST['form_data'], true);
            $warrior_stats = array();
            $this->POST = $POST;
            // if($POST['route'] == 'calculator') {
            //     $this->inputStats();
            // }
            switch ($this->POST['route']) {
                case 'calculator':
                    $this->test_warriors = json_decode($this->POST['warriors']);
                    $this->dbStats(null, true);
                    $this->getDaqloons('other');
                    break;
                case 'adventure':
                    $this->dbStats('adventure');
                    $this->getDaqloons('adventure');
                case 'army mission':
                    $this->dbStats('army mission');
                    $this->getDaqloons('army mission');
                    break;    
                default:
                    return false;
                    break;
            }
            $battle_result = null;
            $this->duration = 1;
            do {
                $this->battle_progress[] = "Hit: " . $this->duration;
                // shuffle($this->warriors);
                
                for($i = 0; $i < count($this->warriors); $i++) {
                    $this->warrior_i = $i;
                    $this->daqloon_i = $i;
                    $warrior = $this->warriors[$i];
                    $daqloon = $this->daqloons[$i];
                    // if(isset($this->daqloons[$i])) {
                    // }
                    // else {
                    //     break;
                    // }
                    
                    $first = rand(1,2);
                    if(($this->duration % 3 !== 0 || $this->duration % 2 !== 0) && $this->warriors[$i]['type'] == 'ranged') {
                        // Different attack speed on different durations
                        if($this->duration % 3 !== 0 && $this->warriors[$i]['type'] === 'ranged' &&
                           $this->warriors[$i]['attack_speed'] == 2) {
                            $this->warriorHit($daqloon, $warrior);   
                        }
                        else if($this->duration % 2 !== 0 && $this->warriors[$i]['type'] === 'ranged' &&
                           $this->warriors[$i]['attack_speed'] == 1) {
                            $this->warriorHit($daqloon, $warrior);   
                        }    
                    }
                    else if($first === 1) {
                        $this->warriorHit($daqloon, $warrior);
                        
                        // If daqlooon is wounded it will not be able to hit
                        if(isset($this->daqloons[$i])) {
                            $this->daqloonHit($daqloon, $warrior);
                        }
                    }
                    else {
                        $this->daqloonHit($daqloon, $warrior);

                        // If warrior is wounded it will not be able to hit
                        if(isset($this->warriors[$i])) {
                            $this->warriorHit($daqloon, $warrior);
                        }
                    }
                    
                    $daqloon_count = count($this->daqloons);
                    $warrior_count = count($this->warriors);
                    if($daqloon_count != $warrior_count) {
                        ($daqloon_count > $warrior_count) ? $array = "daqloons" : $array = "warriors";
                        switch($array) {
                            case "daqloons":
                                $count = round(($daqloon_count - $warrior_count) / $warrior_count);
                                if($count > 4) {
                                    $count = 4;
                                }
                                // Get maximum daqloons in array to calculate which daqloon(s) is left over
                                $x = max(array_keys($this->daqloons));
                                for ($w = 0; $w < $count; $w++) {
                                    if(!isset($this->warriors[$i])) {
                                        // If current warrior is wounded the daqloon(s) will move to the next
                                        $i++;
                                        $this->warrior_i += 1;
                                        if(!isset($this->warriors[$i])) {
                                            // If the next iteration doesn't exists it means that daqloons have won
                                            break;
                                        }
                                    }
                                    if($this->duration % 3 === 0 && $this->warriors[$i]['type'] === 'ranged') {
                                        $this->daqloonHit($this->daqloons[$x - $w - ($i * $count)],
                                                                                $this->warriors[$i], $number = $x - $w - ($i * $count));
                                    }
                                    else if($this->warriors[$i]['type'] != 'ranged') {
                                        $this->daqloonHit($this->daqloons[$x - $w - ($i * $count)],
                                                                                $this->warriors[$i], $number = $x - $w - ($i * $count));
                                    }
                                    // If $this->warriors[$i] isn't isset, it means that the daqloon is wounded
                                    if(!isset($this->warriors[$i])) {
                                        break;
                                    }
                                }
                                /*if(isset($this->warriors[$i]) && (($daqloon_count - $warrior_count) / $warrior_count) % 2 != 0
                                         && $count < 4) {
                                    $this->daqloonHit($this->daqloons[$x  - $w - (($i * $count) -1)], $this->warriors[$i],
                                                      $number = $x - $w - ($i * $count));
                                }*/
                                break;
                            case "warriors":
                                $count = round(($warrior_count - $daqloon_count) / $daqloon_count);
                                if($count > 4) {
                                    $count = 4;
                                }
                                // Get maximum warriors in array to calculate which warrior(s) is left over
                                $x = max(array_keys($this->warriors));
                                for ($w = 0; $w < $count; $w++) {
                                    if(!isset($this->daqloons[$i])) {
                                        // If current warrior is wounded the daqloon(s) will move to the next
                                        $i++;
                                        $this->daqloon_i += 1;
                                        if(!isset($this->daqloons[$i])) {
                                            // If the next iteration doesn't exists it means that daqloons have won
                                            break;
                                        }
                                    }
                                    /*$this->warrior_i += 1;*/
                                    $this->warriorHit($this->daqloons[$i], $this->warriors[$x - $w - ($i * $count)], $i);
                                    // If $this->daqloons[$i] isn't isset, it means that the daqloon is wounded
                                    if(!isset($this->daqloons[$i])) {
                                        break;
                                    }
                                }
                                break;
                        }
                    }
                }
                $this->warriors = array_values($this->warriors);
                $this->daqloons = array_values($this->daqloons);
                
                if($this->warrior_amount <= 0) {
                        $this->battle_result = "Daqloons wins";
                        $this->battle_progress[] = "Daqloons wins";
                        break;
                    }
                else if($this->daqloons <= 0) {
                        $this->battle_result = 'Warriors wins';
                        $this->battle_progress[] = "Warriors wins";
                        break;
                }
                $this->battle_progress[] = "</br>";
                $this->duration++;
                if($this->duration == 30) {
                    $this->battle_progress[] = "Battle went on too long";
                    break;
                }
            }
            while(empty($battle_result));
            $this->getStatistics();
            for($i = 0; $i < count($this->warriors); $i++) {
                $update_data[$i] = array($this->warriors[$i]['health'], $this->warriors[$i]['warrior_id'], $this->username);
            }
            try {
                // $sql2 = "UPDATE warriors SET health = ? WHERE warrior_id = ? AND username = ?";
                // $stmt2 = $this->db->conn->prepare($sql2);
                // foreach($update_data as $key) {
                //     $stmt2->execute($key);
                // }
                $param_battle_result = $battle_result;
                $param_adventure_id = $this->adventure_data['adventure_id'];
                if($this->POST['route'] === 'adventure' ) { 
                    $sql = "UPDATE adventures SET battle_result=:battle_result WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":battle_result", $param_battle_result, PDO::PARAM_STR);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                    $stmt->execute();
                    $param_adventure_id = $this->adventure_data['adventure_id'];
                    $param_daqloon_damage = $this->daqloon_damage;
                    $param_warrior_damage = $this->warrior_damage;
                    $param_daqloon_wounded = $this->stats['wounded']['daqloon'];
                    $param_warrior_wounded = $this->stats['wounded']['warrior'];
                    $param_daqloon_combo = $this->battle_statistics['daqloon_combo'];
                    $param_warrior_combo = $this->battle_statistics['warrior_combo'];
                    $sql2 = "INSERT INTO adventure_battle
                                (adventure_id, daqloon_damage, warrior_damage, warrior_wounded, daqloon_wounded, warrior_combo, daqloon_combo)
                                VALUES(:adventure_id, :daqloon_damage, :warrior_damage, :daqloon_wounded, :warrior_wounded, :daqloon_combo,
                                :warrior_combo)";
                        $stmt2 = $this->db->conn->prepare($sql2);
                        $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                        $stmt2->bindParam(":daqloon_damage", $param_daqloon_damage, PDO::PARAM_INT);
                        $stmt2->bindParam(":warrior_damage", $param_warrior_damage, PDO::PARAM_INT);
                        $stmt2->bindParam(":daqloon_wounded", $param_daqloon_wounded, PDO::PARAM_INT);
                        $stmt2->bindParam(":warrior_wounded", $param_warrior_wounded, PDO::PARAM_INT);
                        $stmt2->bindParam(":daqloon_combo", $param_daqloon_combo, PDO::PARAM_INT);
                        $stmt2->bindParam(":warrior_combo", $param_warrior_combo, PDO::PARAM_INT);
                        $stmt2->execute();
                    }
            }
            catch(Exception $e) {
                throw new Exception("ERROR: Failed to update warrior stats", $e->getMessage());
                return;
            }
            
        }       
        protected function daqloonHit($daqloon, $warrior , $number = false) {
            if(rand(1,100) <= round(($warrior['technique_level'] * (0.75 + ($warrior['block_ratio'] * 0.4))))
               && $warrior['type'] != 'ranged'
               && $warrior['block_ratio'] > 0.9) {
                    $this->battle_progress[] = "Blocked attack from daqloon " . $daqloon['id'];
                    $this->stats['blocked']['warrior'] += 1;
            }
            else {
                $daqloon_hit = round(rand(0, $daqloon['attack']) - $warrior['defence'] - ($this->duration  * 0.4));
                $daqloon_hit = ($daqloon_hit <  0) ? 0 : $daqloon_hit;
                $warrior['health'] -= $daqloon_hit;
                $this->daqloon_damage[] = $daqloon_hit;
                $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " got hit for " . $daqloon_hit . " by daqloon " .
                $daqloon['id'] . ", warrior health: " . $warrior['health'];
                
                if($warrior['health'] < 10) {
                    $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " wounded";
                    $warrior['health'] = 9.5;
                    unset($this->warriors[$this->warrior_i]);
                    $this->stats['wounded']['warrior'] += 1;
                    $this->warrior_amount -= 1;
                }
            }
            // Determine which daqloon to hit
            if($number !== false) {
                $this->daqloons[$number] = $daqloon;
            }
            else {
                $this->daqloons[$this->daqloon_i] = $daqloon;
            } 
            $this->warriors[$this->warrior_i] = $warrior;
        }
        protected function warriorHit($daqloon, $warrior, $second = false, $number = false) {
            $tech_min = (15 - $warrior['technique_level'] < 1) ? 1 :  15 - $warrior['technique_level'];
            $duration_multiplier = ($warrior['stamina_level'] - $this->duration > 0) ? 0 : $warrior['stamina_level'] - 
            $this->duration;
            if(rand(1, 100) >=  66 + ($warrior['technique_level'] * 2)) {
                $this->battle_progress[] = "Attack missed from warrior " . $warrior['warrior_id'];
                $this->stats['missed']['warrior']+= 1;
                $warrior_hit = 0;
            }
            if($warrior['type'] === 'ranged' && !isset($warrior_hit)) {
                if($warrior['ammunition_amount'] <= 0) {
                    
                    $this->battle_progress[] = 'Ammunition empty!';
                    $warrior_hit = 5;
                }
                else {
                    //Hit damage decrease when they are fighting
                    $warrior_hit = round(rand($warrior['precision_level'] * 1, $warrior['attack'] + ($warrior['precision_level'] / 2))
                                        - $daqloon['defence'] - $duration_multiplier);
                    $this->warriors['ammunition_amount'] -= 1;
                    echo "Ammo: " . $this->warriors['ammunition_amount'];
                    $warrior_hit += ($warrior['type'] === 'warrior') ?  : (0.6 * $warrior['precision_level']);
                }
            }
            else if(!isset($warrior_hit)) {
                // Warrior is melee
                //Hit damage decrease when they are fighting
                $warrior_hit = round(rand($warrior['strength_level'] * 1, $warrior['attack'] + ($warrior['strength_level'] / 2))
                                        - $daqloon['defence'] - $duration_multiplier);
            }
            if($warrior_hit <= 0) {
                $warrior_hit = 0;
            }
            $daqloon['health'] -= $warrior_hit;
            $this->warrior_damage[] = $warrior_hit;
            $this->battle_progress[] = "Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by warrior " .
            $warrior['warrior_id'] . ", daqloon health: " . $daqloon['health'];
            if($daqloon['health'] < 10 ) {
                $this->battle_progress[] =  'Daqloon ' . $daqloon['id'] . ' wounded';
                unset($this->daqloons[$this->daqloon_i]);
                $this->stats['wounded']['daqloons'] += 1;
                $this->daqloon_amount -= 1;
            }
            // $number is the index of the daqloon because of uneven numbers of daqloons vs warriors
            if($number !== false) {
                $this->warriors[$number] = $warrior;
            }
            else {
                $this->warriors[$this->warrior_i] = $warrior;
            }
            $this->daqloons[$this->daqloon_i] = $daqloon;    
            
            
            // If the attack_speed is above 1 means that the warrior is carrying dagger or throwing knives
            if($daqloon['health'] > 10  && $warrior['attack_speed'] > 1 && $second !== false) {
                $this->warriorHit($daqloon, $warrior, $second = true, $number);
            }
        }
        public function getStatistics($fetch = false) {
            if($fetch === true) {
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $sql = "SELECT daqloon_damage, warrior_damage, warrior_wounded, daqloon_wounded, warrior_combo, daqloon_combo
                FROM adventure_battles WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $data = array('battle_progress' => $this->battle_progress, 'result'  => $row['result'],
                          'daqloon_damage' => $row['daqloon_damage'], 'warrior_damage' => $row['warrior_damage'],
                          'warrior_wounded' => $row['warrior_wounded'], 'daqloon_wounded' => $row['daqloon_wounded'],
                          'warrior_combo' => $this->combo_attack['warrior'], 'daqloon_combo' => $this->combo_attack['daqloon'],
                          'stats' => $this->stats);

                if($this->adventure_data['battle_result'] == 1) {
                    $this->battle_statistics['result'] = "Warriors wins!";
                }
                else {
                    $this->battle_statistics['result'] = "Daqloons wins!";   
                }
            }
            else {

                $data = array('battle_progress' => $this->battle_progress, 'result'  => $this->battle_result,
                'daqloon_damage' => array_sum($this->daqloon_damage), 'warrior_damage' => array_sum($this->warrior_damage),
                'warrior_wounded' => count($this->warrior_status), 'daqloon_wounded' => count($this->daqloon_status),
                'warrior_combo' => $this->combo_attack['warrior'], 'daqloon_combo' => $this->combo_attack['daqloon'],
                'stats' => $this->stats);
            }
            get_template('battleResult', $data, true);
        }
        protected function getDaqloons($type) {
            if($type === 'army mission') {
                switch ($this->POST['difficulty']) {
                    case 'easy':
                        $this->daqloon_amount = rand(3, 5);
                        break;
                    case 'medium':
                        $this->daqloon_amount = rand(5, 7);
                        break;
                    case 'hard':
                        $this->daqloon_amount = rand(7, 10);
                        break;
                    default:
                        $this->daqloon_amount = 0;
                        break;
                }
            }
            else if($type === 'adventure') {
                switch ($this->POST['difficulty']) {
                    case 'easy':
                        $this->daqloon_amount = rand(3, 6);
                        break;
                    case 'medium':
                        $this->daqloon_amount = rand(5, 8);
                        break;
                    case 'hard':
                        $this->daqloon_amount = rand(7, 11);
                        break;
                    default:
                        $this->daqloon_amount = 0;
                        break;
                }
            }         
            else {
                $this->daqloon_amount = 5;
            }   
            for($i = 0; $i < $this->daqloon_amount; $i++) {
                $this->daqloons[$i] = array('id' => $i + 1, 'health' => 150, 'attack' => 25,
                                           'defence' =>  50);
            }
        }
        protected function dbStats($type, $calc = false) {
            if($calc == false) {

                //Get levels of the soldiers, armour and weapons
                if($type == 'army mission') {
                    $mission_number = 1;
                }
                else {
                    $mission_number = 2;
                }
                $param_username = $this->username;
                $value = intval($mission_number);
                $sql = "SELECT warrior_id FROM warriors WHERE username=:username AND mission = {$value}";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                $query_array = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), "warrior_id");
            }
            else {
                $query_array = $this->test_warriors;
            }
            $query_array[] = $this->username;
            $in  = str_repeat('?,', count($query_array) - 2) . '?';
            
            $sql = "SELECT w.warrior_id, w.type, w.health, wl.stamina_level, wl.technique_level, wl.precision_level,
                    wl.strength_level
                    FROM warriors as w 
                    INNER JOIN warriors_levels as wl ON w.warrior_id = wl.warrior_id
                    WHERE w.warrior_id IN ($in) AND w.username=? GROUP BY w.warrior_id ";
            $stmt = $this->db->conn->prepare($sql);
            $param_username = $this->username;
            $stmt->execute($query_array);
            $warriors = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $sql = "SELECT warrior_id, helm, ammunition, ammunition_amount, left_hand, body, right_hand, legs, boots,
                    (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots) AND item)
                    AS attack,
                    (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                    FROM warrior_armory
            
                    WHERE warrior_id IN ($in) AND username=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i = 0; $i < count($stats); $i++) {
                $stats[$i]['attack'] += 10;
                $stats[$i]['defence'] += 12;
            }
            
            
            $this->warriors = array();
            
            $types = array("dagger", "knives");
            $this->warrior_amount = count($warriors);
            for($q = 0; $q < $this->warrior_amount; $q++) {
                $this->warriors[] = array_merge($warriors[$q], $stats[$q]);
                
                if(strpos($stats[$q]['left_hand'], 'shield') !== false && $warriors[$q]['type'] != 'ranged') {
                    $this->warriors[$q]['block_ratio'] = 2;
                }
                else if(strpos($stats[$q]['left_hand'], 'sword') !== false && $warriors[$q]['type'] != 'ranged') {
                    $this->warriors[$q]['block_ratio'] = 1;
                }
                else {
                    $this->warriors[$q]['block_ratio'] = 0;
                }
                for($i = 0; $i < 2; $i++) {
                    if(strpos($stats[$q]['left_hand'], 'dagger') !== false ||
                       strpos($stats[$q]['left_hand'], 'knives') !== false ) {
                        $this->warriors[$q]['attack_speed'] = 1;
                    }
                    else {
                        $this->warriors[$q]['attack_speed'] = 2;
                    }
                }
            }
            $this->warrior_army = $this->warriors;
        }
    }
?>