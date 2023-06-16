<?php
    class Combat_model extends model {
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
        private $duration;
        private $duration_multiplier;
        private $battle_result = "Battle went on too long";
        private $battle_progress = array();
        private $warrior_damage = array();
        private $daqloon_damage = array();
        private $error = false;
        private $errorHTML = "<p> An error has occured. Please try again </p>";
        private $combo_attack = array('warrior' => 0, 'daqloon' => 0);
        private $blocked = array('warrior' => 0, 'daqloon' => 0);
        private $stats = array("blocked" => array("warrior" => 0, "daqloon" => 0), 
                        "missed" => array('warrior' => 0, 'daqloon' => 0),
                        "wounded" => array('warrior' => 0, 'daqloon' => 0));
        private $weapons = array(
            1 => array('sword', 'bow'),
            2 => array('knife', 'dagger')
        );
        private $warrior_i;
        private $daqloon_i;
        private $POST;

        function __construct ($session, $provided_db = false) {
            if (!is_object($provided_db)) {
                parent::__construct(true);
                $this->provided_db = $this->db;
            } else {
                $this->provided_db = $provided_db;
            }
            $this->username = $session['username'];
            $this->session = $session;
        }

        /**
         * Ca
         *
         * @param [type] $POST
         * @return void
         */
        public function calculateBattleResult($POST) {
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
                    $this->error = true;
                    break;
            }
            if($this->error === true) {
                return $this->errorHTML;
            }
            $battle_result = null;
            $this->duration = 0;
            do {
                for($i = 0; $i < count($this->warriors); $i++) {
                    $this->warrior_i = $i;
                    $this->daqloon_i = $i;
                    $warrior = $this->warriors[$i];
                    // if(!isset($this->warriors[$i])) {
                    //     break;
                    // }
                    if(!isset($this->daqloons[$i])) {
                        $index = $this->findNextTarget($this->daqloons, $i + 1);
                        if($index === false) {
                            break;
                        } else {
                            $this->daqloon_i = $index;
                            $daqloon = $this->daqloons[$this->daqloon_i];
                        }
                    } else {
                        $daqloon = $this->daqloons[$this->daqloon_i];
                    }
                    
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
                }
                if(count($this->daqloons) > count($this->warriors)) {
                    $count = count($this->daqloons) - count($this->warriors);
                    $starting_index = count($this->daqloons) - $count;
                    $i = 0;
                    for($x = 0; $x < $count; $x++) {
                        $index = $this->findNextTarget($this->warriors, $i);
                        if($index === false) {
                            break;
                        } else {
                            $this->warrior_i = $index;
                            $this->daqloon_i = $starting_index + $x;
                            $daqloon = $this->daqloons[$this->daqloon_i];
                            $warrior = $this->warriors[$this->warrior_i];
                            $this->daqloonHit($daqloon, $warrior);
                        }
                        $i++;
                    }
                }
                $this->warriors = array_values(array_filter($this->warriors, 
                    function($element) { return $element['wounded'] === false;}));
                $this->daqloons = array_values(array_filter($this->daqloons, 
                    function($element) { return $element['wounded'] === false;}));
                if(count($this->warriors) === 0) {
                    $this->battle_result = "Daqloons wins";
                    $this->battle_progress[$this->duration][] = "Daqloons wins";
                    break;
                }
                else if(count($this->daqloons) === 0) {
                    $this->battle_result = 'Warriors wins';
                    $this->battle_progress[$this->duration][] = "Warriors wins";
                    break;
                }
            $this->duration++;
            $this->duration_multiplier = ($this->duration * 0.4 > 10) ? 10 : $this->duration_multiplier * 0.4;
            if($this->duration == 100) {
                $this->battle_progress[$this->duration][] = "Battle went on too long";
                break;
            }
            }
            while(empty($battle_result));
            for($i = 0; $i < count($this->warriors); $i++) {
                $update_data[$i] = array($this->warriors[$i]['health'], $this->warriors[$i]['warrior_id'], $this->username);
            }
            try {
                if($this->POST['route'] === 'adventure' ) { 
                    $param_battle_result = $battle_result;
                    $param_adventure_id = $this->adventure_data['adventure_id'];
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
            return $this->getStatistics();
        }
        private function findNextTarget($targets, $starting_index) {
            $index = false;
            for($i = $starting_index; $i < count($targets); $i++) {
                if($targets[$i]['wounded'] === false) {
                    $index = $i;
                    break;
                }
            }
            if($index === null) {
                for($i = 0; $i < count($targets); $i++) {
                    if($targets[$i]['wounded'] === false) {
                        $index = $i;
                        break;
                    }
                }
                return false;
            }
            return $index;
        }   
        protected function daqloonHit($daqloon, $warrior , $number = false) {
            if(rand(1,100) <= round(($warrior['technique_level'] * (0.75 + ($warrior['block_ratio'] * 0.4))))
               && $warrior['type'] != 'ranged'
               && $warrior['block_ratio'] > 0.9) {
                    $this->battle_progress[$this->duration][] = "Blocked attack from daqloon " . $daqloon['id'];
                    $this->stats['blocked']['warrior'] += 1;
            }
            else {
                $daqloon_hit = round($daqloon['attack']) - $warrior['defence'] - $this->duration_multiplier * (10 / rand(8, 12));
                $daqloon_hit = ($daqloon_hit <  0) ? 0 : $daqloon_hit;
                $warrior['health'] -= $daqloon_hit;
                $this->daqloon_damage[] = $daqloon_hit;
                $this->battle_progress[$this->duration][] = "Warrior " . $warrior['warrior_id'] . " got hit for " . $daqloon_hit . " by daqloon " .
                $daqloon['id'] . ", warrior health: " . $warrior['health'];
                
                if($warrior['health'] < 10) {
                    $this->battle_progress[$this->duration][] = "Warrior " . $warrior['warrior_id'] . " wounded";
                    $warrior['health'] = 9.5;
                    $warrior['wounded'] = true;
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
            if(rand(1, 100) >=  66 + ($warrior['technique_level'] * 2)) {
                $this->battle_progress[$this->duration][] = "Attack missed from warrior " . $warrior['warrior_id'];
                $this->stats['missed']['warrior']+= 1;
                $warrior_hit = 0;
            }
            if($warrior['type'] === 'ranged' && !isset($warrior_hit)) {
                if($warrior['ammunition_amount'] <= 0) {
                    
                    $this->battle_progress[$this->duration][] = $warrior['warrior_id'] . ' ammunition empty!';
                    $warrior_hit = 5;
                }
                else {
                    //Hit damage decrease when they are fighting
                    $warrior_hit = round(rand($warrior['precision_level'] * 1, $warrior['attack'] + ($warrior['precision_level'] / 2))
                                        - $daqloon['defence'] - $this->duration_multiplier);
                    $warrior['ammunition_amount'] -= 1;
                    $warrior_hit += ($warrior['type'] === 'warrior') ?  : (0.6 * $warrior['precision_level']);
                }
            }
            else if(!isset($warrior_hit)) {
                // Warrior is melee
                //Hit damage decrease when they are fighting
                $warrior_hit = round(rand($warrior['strength_level'] * 1, $warrior['attack'] + ($warrior['strength_level'] / 2))
                                        - $daqloon['defence'] - $this->duration_multiplier);
            }
            if($warrior_hit <= 0) {
                $warrior_hit = 0;
            }
            $daqloon['health'] -= $warrior_hit;
            $this->warrior_damage[] = $warrior_hit;
            $this->battle_progress[$this->duration][] = "Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by warrior " .
            $warrior['warrior_id'] . ", daqloon health: " . $daqloon['health'];
            if($daqloon['health'] < 10 ) {
                $this->battle_progress[$this->duration][] =  'Daqloon ' . $daqloon['id'] . ' wounded';
                $this->stats['wounded']['daqloon'] += 1;
                $warrior['wounded'] = true;
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
            ob_start();
            get_template('battleResult', $data, true);
            return ob_get_clean();
            
        }
        protected function getDaqloons($type) {
            if($type === 'army mission') {
                switch ($this->POST['difficulty']) {
                    case 'easy':
                        $this->daqloon_amount = rand(5, 7);
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
                        $this->daqloon_amount = rand(5, 7);
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
                $this->daqloons[$i] = array('id' => $i + 1, 'health' => 150, 'attack' => 75,
                                           'defence' =>  50, 'wounded' => false);
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
                $sql = "SELECT warrior_id FROM warriors WHERE username=:username AND mission = {$mission_number}";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $query_array = array_column($row, "warrior_id");
            }
            else {
                $query_array = $this->test_warriors;
            }
            if(!count($query_array) > 0) {
                $this->error = true;
                return $this->errorHTML;
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
                $this->warriors[$q] = array_merge($warriors[$q], $stats[$q]);
                $this->warriors[$q]['wounded'] = false;
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