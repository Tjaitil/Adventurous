<?php
    class CombatCalculator_model extends model {
        public $username;
        public $session;
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
        private $weapons = array(
            1 => array('sword', 'bow'),
            2 => array('knife', 'dagger')
        );
        private $duration;
        private $warrior_i;
        private $daqloon_i;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function calculate($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to calculate the fight result based on the provided information
            $this->data = json_decode($POST['form_data'], true);
            $warrior_stats = array();

            if($POST['route'] == 'calculator') {
                $this->inputStats();
            }
            else if($POST['route'] == 'db') {
                $this->dbStats(); 
            }
            var_dump($this->warriors);
            $battle_result;
            $this->duration = 1;
            do {
                $this->battle_progress[] = "Hit: " . $this->duration;
                // shuffle($this->warriors);
                for($i = 0; $i < count($this->warriors); $i++) {
                    $this->warrior_i = $i;
                    $this->daqloon_i = $i;
                    $warrior = $this->warriors[$i];
                    $daqloon = $this->daqloons[$i];
                    
                    $first = rand(1,2);
                    if(($this->duration % 3 !== 0 || $this->duration % 2 !== 0) && $this->warriors[$i]['type'] == 'ranged') {
                        if($this->duration % 3 !== 0 && $this->warriors[$i]['type'] === 'ranged' &&
                           $this->warriors[$i]['attack_speed'] == 1) {
                            $this->daqloons[$i] = $this->warriorHit($daqloon, $warrior);   
                        }
                        if($this->duration % 2 !== 0 && $this->warriors[$i]['type'] === 'ranged' &&
                           $this->warriors[$i]['attack_speed'] == 1) {
                            $this->daqloons[$i] = $this->warriorHit($daqloon, $warrior);   
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
                                $count = floor(($daqloon_count - $warrior_count) / $warrior_count);
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
        }       
        protected function daqloonHit($daqloon, $warrior , $number = false) {
            if(rand(1,100) <= round(($warrior['technique_level'] * (0.75 + ($warrior['block_ratio'] * 0.4))))
               && $warrior['type'] != 'ranged'
               && $warrior['block_ratio'] > 0.9) {
                    $this->battle_progress[] = "Blocked attack from daqloon " . $daqloon['id'];
                    $this->blocked['warrior'] += 1;
            }
            else {
                $daqloon_hit = $daqloon['attack'] + rand(1,3) * (0.025 * $warrior['defence']);
                $warrior['health'] -= $daqloon_hit;
                $this->daqloon_damage[] = $daqloon_hit;
                $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " got hit for " . $daqloon_hit . " by daqloon " .
                $daqloon['id'] . ", warrior health: " . $warrior['health'];
                
                if($warrior['health'] < 10) {
                    $this->battle_progress[] = "Warrior " . $warrior['warrior_id'] . " wounded";
                    $warrior['health'] = 9.5;
                    unset($this->warriors[$this->warrior_i]);
                    $this->warrior_amount -= 1;
                }
            }
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
            if(rand(1, 100) <= $warrior['technique_level']) {
                $this->battle_progress[] = "Attack missed from warrior " . $warrior['warrior_id'];
                $warrior_hit = 0;
            }
            if($warrior['ammunition'] === 0 && $warrior['type'] === 'ranged') {
                $this->battle_progress[] = 'Ammunition empty!';
                $daqloon['health'] -= 5;
                $this->daqloons[$duration] = $daqloon;
                $warrior_hit = 0;
            }
            if($warrior['type'] === 'ranged' && !isset($warrior_hit)) {
                $warrior_hit = $warrior['attack'] - (0.025 * $daqloon['defence']);
                         - ($warrior['stamina_level'] - ($this->duration * 0.2)); //Hit damage decrease when they are fighting
                $this->warriors['ammunition'] -= 1;
                echo "Ammo: " . $this->warriors['ammunition'];
            }
            else if(!isset($warrior_hit)) {
                $warrior_hit = $warrior['attack'] - (0.025 * $daqloon['defence']);
                         - ($warrior['stamina_level'] - ($this->duration * 0.2)); //Hit damage decrease when they are fighting    
            }
            $warrior_hit += ($warrior['type'] === 'warrior') ? (0.5 * $warrior['strength_level']) : (0.6 * $warrior['precision_level']);
            $daqloon['health'] -= ($warrior_hit < 0) ? 0 : $warrior_hit;
            $this->warrior_damage[] = $warrior_hit;
            $this->battle_progress[] = "Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by warrior " .
            $warrior['warrior_id'] . ", daqloon health: " . $daqloon['health'];
            if($daqloon['health'] < 10 ) {
                $this->battle_progress[] =  'Daqloon ' . $daqloon['id'] . ' wounded';
                unset($this->warriors[$this->daqloon_i]);
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
        public function getStatistics() {
            $data = array('battle_progress' => $this->battle_progress, 'result'  => $this->battle_result,
                          'daqloon_damage' => array_sum($this->daqloon_damage), 'warrior_damage' => array_sum($this->warrior_damage),
                          'warrior_wounded' => count($this->warrior_status), 'daqloon_wounded' => count($this->daqloon_status),
                          'warrior_combo' => $this->combo_attack['warrior'], 'daqloon_combo' => $this->combo_attack['daqloon'],
                          'warrior_blocked' => $this->blocked['warrior'], 'daqloon_blocked' => $this->blocked['daqloon']);
            get_template('calculatorResult', $data, true);
        }
        
        protected function calculateDamage($warrior) {
            
            // Calculate the damage number from the amour type
            switch($type_armour) {
                case 'iron':
                    $armor_number;
                    break;
                case 'steel':
                    $armor_number;
                    break;
                case 'gargonite':
                    $armor_number;
                    break;
                case 'yeqdon':
                    $armor_number;
                    break;
                case 'adron':
                    $armor_number;
                    break;
                case 'frajrite':
                    $armor_number;
                    break;
                case 'wujkin':
                    $armor_number;
                    break;
            }
            
            $health = $around; 
            // Calculate the health number from health amount
            
        }
        protected function dbStats() {
            //Get levels of the soldiers, armour and weapons
            
            $query_array = $this->data['warriors'];
            $query_array[] = $this->username;
            var_dump($this->username);
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
            var_dump($warriors);
            $sql = "SELECT warrior_id, helm, ammunition, left_hand, body, right_hand, legs, boots,
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
            $this->daqloon_amount = 5;
            for($i = 0; $i < $this->daqloon_amount; $i++) {
                $this->daqloons[$i] = array('id' => $i + 1, 'health' => 150, 'attack' => 15,
                                           'defence' =>  50);
            }
        }
        protected function inputStats() {
             $total_amount = intval($data['melee_amount']) + intval($data['ranged_amount']);
            $total_daqloon = intval($data['daqloon_amount']);
            for($i = 0; $i < $total_amount; $i++) {
                $this->warriors[$i] = array('warrior_id' => $i + 1, 'health' => 100);
                if($i < $data['melee_amount']) {
                    $this->warriors[$i]['type'] = 'melee';
                }
                else {
                    $this->warriors[$i]['type'] = 'ranged';
                }
            }
            
            if($data['stats_type'] === 'group') {
                for($i = 0; $i < count($this->warriors); $i++) {
                    $this->warriors[$i]['stamina_level'] = $data['stamina_level'];
                    $this->warriors[$i]['technique_level'] = $data['technique_level'];
                    $this->warriors[$i]['precision_level'] = $data['precision_level'];
                    $this->warriors[$i]['strength_level'] = $data['strength_level'];
                    if($this->warriors[$i]['type'] === 'melee') {
                        $this->warriors[$i]['attack'] = $data['melee_attack'];
                        $this->warriors[$i]['defence'] = $data['ranged_defence'];
                    }
                    else if($this->warriors[$i]['type'] === 'ranged') {
                        $this->warriors[$i]['attack'] = $data['ranged_attack'] + 10;
                        $this->warriors[$i]['defence'] = $data['ranged_defence'];
                        $this->warriors[$i]['ammunition'] = 10;
                    }
                }
            }
            else if($data['stats_type'] === 'individ') {
                foreach($data as $key) {
                    if(substr($key, 0, 7) === 'warrior') {
                        $warrior_stats[$key] = explode(',', $key);
                    }
                }
                for($i = 0; $i < count($this->warriors); $i++) {
                    $this->warriors[$i]['stamina_level'] = $warrior_stats[$i][0];
                    $this->warriors[$i]['technique_level'] = $warrior_stats[$i][1];
                    $this->warriors[$i]['precision_level'] = $warrior_stats[$i][2];
                    $this->warriors[$i]['strength_level'] = $warrior_stats[$i][3];
                    $this->warriors[$i]['attack'] = $warrior_stats[$i][4];
                    $this->warriors[$i]['defence'] = $warrior_stats[$i][5];
                }
            }
            
            for($i = 0; $i < $total_daqloon; $i++) {
                $this->daqloons[$i] = array('id' => $i + 1, 'health' => 150, 'attack' => $data['daqloon_attack'],
                                           'defence' => $data['daqloon_defence']);
            }
            $this->warriors[0]['health'] = 200;
            $this->daqloons[0]['health'] = 100;
        }
    }
?>