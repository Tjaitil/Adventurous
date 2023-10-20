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
            $param_username = $this->username;
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $adventure_id = $row['adventure_id'];
            
            if(!$adventure_id > 0) {
                $this->response->addTo("errorGameMessage", "You don't have any adventures!");
                return false;
            }
            
            $param_adventure_id = $adventure_id;
            $sql = "SELECT difficulty, location, farmer, miner, trader, warrior, adventure_status, adventure_countdown,
                    battle_result FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->execute();
            $this->adventure_data = $stmt->fetch(PDO::FETCH_ASSOC);
            $time = date("Y-m-d H:i:s");
            if($time < $this->adventure_data['adventure_countdown']) {
                $this->response->addTo("errorGameMessage", "The adventure is not yet finished!");
                return false;
            }
            $this->adventure_data['adventure_id'] = $adventure_id;
            $Combat_model = $this->loadModel('Combat', true, true);
            if($this->adventure_data['battle_result'] == 0) {
                $Combat_model->calculate(array("route" => "adventure", "difficulty" => $this->adventure_data['difficulty']));
            }
            else {
                // Fetch data
                $Combat_model->getStatistics(true);
            }
            $role = array_search($this->username, $this->adventure_data);
            if($role === false) {
                $this->response->addTo("errorGameMessage", "Something unexpected happened, please try again");
                return false;
            }
            else {
                $this->updateRole($role);
            }
        }
        public function updateRole($role) {
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $sql = "SELECT battle_result FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->execute();
            $battle_result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $param_location = $this->adventure_data['location'];
            $param_difficulty = $this->adventure_data['difficulty'];
            $sql2 = "SELECT user_xp, warrior_xp_min, warrior_xp_max FROM adventures_data WHERE location=:location AND difficulty=:difficulty";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt2->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt2->execute();
            $xp_data = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            $param_username = $this->username;
            $sql = "SELECT COUNT(item) as item_count FROM inventory WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $item_count = $stmt->fetch(PDO::FETCH_OBJ)->item_count;
            
            switch($this->adventure_data['difficulty']) {
                case 'easy':
                    $count = 2;
                    $xp_data['adventurer_xp'] = 0.5;
                    break;
                
                case 'medium':
                    $count = 3;
                    $xp_data['adventurer_xp'] = 0.3;
                    break;
                
                case 'hard':
                    $count = 5;
                    if($role === 'warrior') {
                        $count -= 1;
                    }
                    $xp_data['adventurer_xp'] = 0.25;
                    break;
            }
            // If player doesn't have enough inventory spaces, echo error message and return
            if($item_count + $count > 18) {
                $this->response->addTo("errorGameMessage", "You need to have at least {$count} empty spaces in your inventory");
                return false;
            }
            
            $rewards = array();
            $warrior_xp = array();
            switch($role) {
                case 'farmer':
                case 'miner':
                case 'trader':
                    // Do it is so there is a high chance of getting a high reward
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty
                            ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $stmt->execute();
                    $rewards[] = $stmt->fetch(PDO::FETCH_ASSOC);
                    $count--;

                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_name = $rewards[0]['item'];
                    $sql2 = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty <= :difficulty AND item !=:name
                            ORDER BY RAND() LIMIT {$count}";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt2->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt2->bindParam(":name", $param_name, PDO::PARAM_STR);
                    $stmt2->execute();
                    while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                        $rewards[] = $row;
                    }
                    break;
                
                case 'warrior':
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty=:difficulty
                            ORDER BY RAND() LIMIT 1";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                    $stmt->execute();
                    $rewards[] = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $param_role = $role;
                    $param_difficulty = $this->adventure_data['difficulty'];
                    $param_name = $rewards[0]['item'];
                    $sql = "SELECT item, min_amount, max_amount FROM adventure_rewards
                            WHERE role=:role AND difficulty <= :difficulty AND item != :name
                            ORDER BY RAND() LIMIT {$count}";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt->bindParam(":name", $param_name, PDO::PARAM_STR); 
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $rewards[] = $row;
                    }
                    
                    $sql = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp, warrior_id, username FROM warriors_levels
                            WHERE warrior_id IN (SELECT warrior_id FROM warriors WHERE mission=1 AND username=:username)
                                  AND username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $warriors_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $warrior_xp;
                    for($i = 0; $i < count($warriors_levels); $i++) {
                        $warrior_xp[$i]['warrior_id'] = $warriors_levels[$i]['warrior_id'];
                        $warrior_xp[$i]['stamina_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warriors_levels[$i]['stamina_xp'] += $warrior_xp[$i]['stamina_xp'];
                        $warrior_xp[$i]['technique_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warriors_levels[$i]['technique_xp'] += $warrior_xp[$i]['technique_xp'];
                        $warrior_xp[$i]['precision_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warriors_levels[$i]['precision_xp'] += $warrior_xp[$i]['precision_xp'];
                        $warrior_xp[$i]['strength_xp'] = rand($xp_data['warrior_xp_min'], $xp_data['warrior_xp_max']);
                        $warriors_levels[$i]['strength_xp'] += $warrior_xp[$i]['strength_xp'];
                    }
            }
            $crystal_chance = $this->calculateCrystal();
            $roles = array("farmer", "miner", "trader", "warrior");
            unset($roles[$role]);
            
            $nones = 0;
            for($i = 0; $i < count($roles); $i++) {
                if($this->adventure_data[$roles[$i]] ==  'none') {
                    $nones++;    
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
                
                // If the new count after transaction is above 18 include Stockpile_model to directly insert items into stockpile
                if(count($this->session['inventory']) + count($rewards) > 18) {
                    $Stockpile_model = $this->loadModel('Stockpile', true);
                }
                
                foreach($rewards as $key) {
                    $this->UpdateGamedata->updateInventory($key['item'], $key['amount'], true);    
                    // Update inventory
                    /*if(count($_SESSION['gamedata']['inventory']) + 1 > 18) {
                        $Stockpile_model->updateStockpile($key['item'], $key['amount']);  
                    }
                    else {
                        $this->UpdateGamedata->updateInventory($key['item'], $key['amount'], true);    
                    }*/
                }
                
                if($crystal_chance === 1) {
                    $rewards[] = array("item" => $this->adventure_data['location'] . ' crystal', "amount" => 1);
                    // Update inventory
                    $this->UpdateGamedata->updateInventory(strtolower($this->adventure_data['location']) . ' crystal', 1, true);
                }
                
                // Only gain xp when farmer level is below 30 or if profiency is farmer
                if($this->session[$role]['level'] < 30 || $this->session['profiency'] == $role) { 
                    $this->response->addTo("levelUP",$this->UpdateGamedata->updateXP($role, $xp_data['user_xp'])); 
                }
                $param_adventurer_respect = $xp_data['adventurer_xp'] + $this->session['adventurer'];
                $param_username = $this->username;
                $sql = "UPDATE user_levels SET adventurer_respect=:respect WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":respect", $param_adventurer_respect, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $sql = "DELETE FROM adventure_requirements WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE adventure SET adventure_id=0, adventure_status=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();

                // If count of roles is 3, then the player getting the report is the last player in the adventure
                if((count($roles) - 1) - $nones == 0) {
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
                    $sql = "UPDATE warriors_levels SET stamina_xp=?, technique_xp=?, precision_xp=?, strength_xp=?
                            WHERE warrior_id=? AND username=?";
                    $stmt = $this->db->conn->prepare($sql);
                    foreach($warriors_levels as $key) {
                        $stmt->execute(array_values($key));
                    }
                    $sql2 = "UPDATE warriors SET mission=0 WHERE mission=1 AND username=:usernae";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt2->execute();
                }
        
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $_SESSION['gamedata']['adventurer'] = $param_adventurer_respect;
            $this->adventure_data['user_xp'] = $xp_data['user_xp'];
            $this->adventure_data['role'] = $role;
            
            ob_start();
            // get_template to show user
            get_template('adventure_rewards', array('adventure_data' => $this->adventure_data,
                                                    'rewards' => $rewards,
                                                    'statistics' => $this->battle_statistics,
                                                    'warrior_xp' => $warrior_xp, 'stockpile_insert' =>
                                                    (isset($Stockpile_model)) ? 'true' : 'false'), true);
            $this->response->addTo("html", ob_get_clean());
            
        }
        protected function calculateCrystal() {
            $param_difficulty = $this->adventure_data['difficulty'];
            $param_location = $this->adventure_data['location'];
            $sql = "SELECT min_chance, max_chance FROM adventure_crystals WHERE difficulty=:difficulty AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
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
    }
?>