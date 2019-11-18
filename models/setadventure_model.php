<?php
    class SetAdventure_model extends model {
        public $username;
        public $session;
        public $adventure_data;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function checkAdventure() {
            // Check if user has an already existing adventure
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        public function toggleInvite() {
            $sql = "SELECT invite_only FROM adventures WHERE adventure_leader=:adventure_leader";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 1) {
                $this->gameMessage("ERROR: You are currently not in an adventure", true);
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "UPDATE adventures SET invite_only=:invite_only WHERE adventure_leader=:adventure_leader";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":invite_only", $param_invite_only, PDO::PARAM_INT);
            $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
            $param_invite_only = ($row['invite_only'] == 1) ? 0 : 1;
            $param_adventure_leader = $this->username;
            $stmt->execute();
            echo ($param_invite_only == 1) ? 'on' : 'off';
        }
        public function newAdventure($adventure_data) {
            $this->adventure_data = $adventure_data;
            $categories = array();
            
            $profiences = array('farmer', 'miner', 'trader', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                print "ERROR";
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                $sql = "INSERT INTO adventures (adventure_leader, difficulty, location, {$this->session['profiency']}, invite_only)
                        VALUES (:adventure_leader, :difficulty, :location, :profiency, :invite_only)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt->bindParam(":invite_only", $param_invite_only, PDO::PARAM_INT);
                $param_adventure_leader = $this->username;
                $param_difficulty = $this->adventure_data['difficulty'];
                $param_location = $this->adventure_data['location'];
                $param_profiency = $this->username;
                $param_invite_only = $this->adventure_data['invite_only'];
                $stmt->execute();
                $this->adventure_data['adventure_id'] = $this->db->conn->lastInsertId();
                
                $sql2 = "UPDATE adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_username = $this->username;
                $stmt2->execute();
                
                $this->advRequirements();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }   
        private function advRequirements() {
            $role = $this->session['profiency'];
            $difficulty = $this->adventure_data['difficulty'];
            $difficulties = array();
            $difficulties['easy'] = array("grade" => 1, "multiplier" => 1);
            $difficulties['medium'] = array("grade" => 2, "multiplier" => 1.3);
            $difficulties['hard'] = array("grade" => 3, "multiplier" => 1.5);
            
            $requirments_amount = 2;
            $adventure_req = array();
            $adventure_req['trader'] = array("easy" => 2/3, "medium" => 4/6, "hard" => 8/10);
            $adventure_req['item_amount'] = array("easy" => 3, "medium" => 4, "hard" => 5);
            $adventure_req['warrior_amount'] = array("easy" => "2/3", "medium" => "4/8", "hard" => "10/15");
            $adventure_req['warrior'] = array("melee", "ranged");
            $requirments = array();
            
            if($role == 'trader') {
                $rand = explode("/", $adventure_req['trader'][$difficulty]);
                $requirments[] = array("requirement" => "diplomacy", 'amount' => rand($rand[0], $rand[1]));
            }
            else if($role == 'warrior') {
                $rand = explode("/", $adventure_req['warrior_amount'][$this->adventure_data['difficulty']]);
                $rand = rand($rand[0], $rand[1]);
                for($i = 0; $i < $rand; $i++ ) {
                    $requirements[] = $adventure_req['warrior'][array_rand($adventure_req['warrior'])];
                }
                $counts = array_count_values($requirements);
                $requirements = array();
                foreach($counts as $key => $value) {
                    $requirements[] = array("requirement" => $key . ' warrior', "amount" => $value);
                }
            }
            else {
                //If $role is warrior or miner
                //Query to get the item requirements
                $count = $adventure_req['item_amount'][$difficulty] - 1;
            
                $sql = "SELECT name FROM adventure_req_items WHERE role=:role AND difficulty=:difficulty
                    ORDER BY rand() LIMIT 1;";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                $param_role = $role;
                $param_difficulty = $difficulties[$difficulty]['grade'];
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
                $sql = "(SELECT name as requirement, difficulty, amount_min, amount_max
                    FROM adventure_req_items WHERE role=:role AND difficulty=:difficulty AND name=:name)
                    UNION ALL
                    (SELECT name as requirement, difficulty, amount_min, amount_max FROM adventure_req_items
                    WHERE role=:role AND difficulty <= :difficulty AND name != :name
                    ORDER BY RAND() LIMIT {$count})";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
                $param_role = $role;
                $param_difficulty = $difficulties[$difficulty]['grade'];
                $param_name = $row['name'];
                $stmt->execute();
                
                $i = 0;
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $requirements[] = $row;
                }
            }
            
            $sql = "INSERT INTO adventure_requirements (adventure_id, username, role, required, amount)
            VALUES (:adventure_id, :username, :role, :required, :amount)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
            $param_username = $this->username;
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $param_role = $this->session['profiency'];
            foreach($requirements as $key) {
                $param_required = $key['requirement'];
                if(in_array($role, array("farmer", "miner")) === true) {
                    $param_amount = rand($key['amount_min'], $key['amount_max']);
                    ($key['difficulty'] < $difficulties[$difficulty]['grade']) ?
                    round($param_amount*= $difficulties[$difficulty]['multiplier']) : "";
                }
                else {
                    $param_amount = $key['amount'];
                }
                $stmt->execute();
            }
        }   
        public function provide($item, $quantity, $warrior_check) {
            $route = ($warrior_check == true) ? "warrior" : "item"; 
            if($this->session['profiency'] == 'trader') {
                $this->gameMessage("The trader doesn't have to provide with anything");
                return false;
            }
            $profiences = array('farmer', 'miner', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                $this->gameMessage("ERROR", true);
                return false;
            }
            
            $sql = "SELECT adventure_id, farmer, miner, trader, warrior, difficulty, location FROM adventures
                    WHERE :username IN (farmer, miner, warrior)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are not in an adventure!", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $row['role'] = array_search($this->username, $row);
            
            if($route === 'warrior') {
                $queryArray = explode(",", $warrior_check);
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                $sql = "SELECT warrior_id, type FROM warriors WHERE warrior_id IN ($in) AND fetch_report=0 AND mission=0 AND username=?";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($queryArray);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: One or more of your warriors are not ready to fight!", true);
                    return false;
                }
                $row3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                var_dump($row3);
                $item = $row3[0]['type'] . ' warrior';
            }
        
            $sql = "SELECT required, amount, provided
                    FROM adventure_requirements WHERE adventure_id=:adventure_id AND role=:role AND required=:required";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
            $param_adventure_id = $row['adventure_id'];
            $param_role = $row['role'];
            $param_required = $item;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are trying to provide the wrong item", true);
                return false;
            }
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row2['provided'] == $row2['amount']) {
                $this->gameMessage("ERROR: You have provided enough!", true);
                return false;
            }
            if($route === 'item') {
                $amount = get_item($this->session['inventory'], $item)['amount'];
                if($amount < $quantity) {
                    $this->gameMessage("ERROR: Not enough in inventory!", true);
                    return false;
                }
            }     
            else if ($route === 'warrior') {
                $warrior_type = array("melee" => 0, "ranged" => 0);
                foreach($row3 as $key) {
                    $warrior_type[$key['type']] += 1;
                }
                if(in_array(0, $warrior_type) == false) {
                    $this->gameMessage("ERROR: One of the selected warriors is a different type", true);
                    return false;
                }
                $quantity = count($row3);
            }
            
            $missing_contribution = $row2['amount'] - $row2['provided']; 
            $missing_contribution = $missing_contribution - $quantity;
            ($missing_contribution == 0)? $status = 1: $status = 0;
            try {
                $this->db->conn->beginTransaction(); 
                
                $sql = "UPDATE adventure_requirements SET provided=:provided, status=:status
                        WHERE adventure_id=:adventure_id AND role=:role AND required=:required";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":provided", $param_provided, PDO::PARAM_STR);
                $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
                $param_provided = $quantity + $row2['provided'];
                $param_status = $status;
                $param_adventure_id = $row['adventure_id'];
                $param_role = $row['role'];
                $param_required = $item;
                $stmt->execute();
                
                if($route === 'item') {
                    update_inventory($this->db->conn, $this->username, $item, -$quantity, true);
                }
                else if ($route === 'warrior') {
                    $sql2 = "UPDATE warriors SET mission=1 WHERE warrior_id IN ($in) AND username=?";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->execute($queryArray);
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            
            if($route == 'item') {
                $string = "You have provided %d of %s";
                $this->gameMessage(sprintf($string, $quantity, $item), true);
            }
            else {
                $string = "You have provided %s warriors";
                $this->gameMessage(sprintf($string, $quantity), true);
            }
            echo "#";
            get_inventory($this->db->conn, $this->username);
            $sql = "SELECT role, required, amount, provided, status FROM adventure_requirements WHERE adventure_id=:adventure_id AND
            role IN ('farmer', 'miner', 'trader', 'warrior')
                    ORDER BY role ASC";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $row['adventure_id'];
            $stmt->execute();
            $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            get_template('requirements', $requirements, true);
            if($route === 'warrior') {
                $sql = "SELECT w.warrior_id, w.type, wl.stamina_level, wl.technique_level, wl.precision_level, wl.strength_level
                        FROM warriors as w
                        INNER JOIN warrior_levels as wl ON wl.warrior_id = w.warrior_id AND wl.username = w.username
                        WHERE w.mission=0 AND w.training_type='none' AND w.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $data['warriors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                get_template('warrior_adventure', $data['warriors'], true);
            }
            $this->db->closeConn();
        }
    }
?>