<?php
    class CombatCalculator_model extends model {
        public $username;
        public $session;
        public $daqloon_status = array();
        public $warrior_status = array();
        public $warriors;
        public $daqloons;
        public $battle_progress = array();
        public $warrior_damage = array();
        public $daqloon_damage = array();
        public $combo_attack = array('warrior' => 0, 'daqloon' => 0);
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        public function calculate($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to calculate the fight result based on the provided information
            $data = json_decode($POST['form_data'], true);
            $warrior_stats = array();
            
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
                        $this->warriors[$i]['attack'] = $data['ranged_attack'];
                        $this->warriors[$i]['defence'] = $data['ranged_defence'];
                    }
                }
            }
            else if($data['type'] === 'individ') {
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
                        $this->battle_progress[] = "Daqloons wins";
                        break;
                    }
                else if(count($this->daqloon_status) === $total_daqloon) {
                        $this->battle_result = 'Warriors wins';
                        $this->battle_progress[] = "Warriors wins";
                        break;
                }
                $this->battle_progress[] = "</br>";
                $duration++;
                if($duration == 30) {
                    $this->battle_progress[] = "Battle went on too long";
                    break;
                }
            }
            while(empty($battle_result));
            $this->getStatistics();
        }
        
        protected function daqloonHit($daqloon, $warrior) {
            if(rand(1,100) <= round($warrior['technique_level'] * 0.25)) {
                    $this->battle_progress[] = "Blocked";
            }
            else {
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
            }
            return $warrior;
        }
        protected function warriorHit($daqloon, $warrior, $duration) {
            $warrior_hit = $warrior['attack'] - (0.025 * $daqloon['defence']);
                         - ($warrior['stamina_level'] - ($duration * 0.2)); //Hit damage decrease when they are fighting
            
            $warrior_hit += ($warrior['type'] === 'warrior') ? (0.5 * $warrior['strength_level']) : (0.6 * $warrior['precision_level']);
            $daqloon['health'] -= $warrior_hit;
            $this->warrior_damage[] = $warrior_hit;
            $this->battle_progress[] = "Daqloon " .  $daqloon['id'] . " got hit for " . $warrior_hit . " by warrior " .
            $warrior['warrior_id'] . ", daqloon health: " . $daqloon['health'];
            if(rand(1,100) <= round($warrior['technique_level'] * 0.4) && $warrior['type'] != 'ranged') {
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
        
        public function getStatistics() {
            $data = array('battle_progress' => $this->battle_progress, 'result'  => $this->battle_result,
                          'daqloon_damage' => array_sum($this->daqloon_damage), 'warrior_damage' => array_sum($this->warrior_damage),
                          'warrior_wounded' => count($this->warrior_status), 'daqloon_wounded' => count($this->daqloon_status),
                          'warrior_combo' => $this->combo_attack['warrior'], 'daqloon_combo' => $this->combo_attack['daqloon']);
            get_template('calculatorResult', $data, true);
        }
    }
?>