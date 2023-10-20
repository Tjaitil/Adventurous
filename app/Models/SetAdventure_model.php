<?php
    class SetAdventure_model extends model {
        public $username;
        public $session;
        public $adventure_data;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function toggleInvite() {
            $param_username = $this->username;
            $sql = "SELECT invite_only FROM adventures WHERE adventure_leader=:adventure_leader";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_leader", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 1) {
                $this->response->addTo("errorGameMessage" , "You are currently not in an adventure");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $param_invite_only = ($row['invite_only'] == 1) ? 0 : 1;
            $param_adventure_leader = $this->username;
            $sql = "UPDATE adventures SET invite_only=:invite_only WHERE adventure_leader=:adventure_leader";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":invite_only", $param_invite_only, PDO::PARAM_INT);
            $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
            $stmt->execute();
            echo ($param_invite_only == 1) ? 'on' : 'off';
        }
        public function checkAdventureStatus($status = false) {
            if($status === false) {
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $sql = "SELECT required, amount, provided
                FROM adventure_requirements WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->execute();
                $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $status = array_column($row2, 'status');
            }
            
            // If everyone has provided enough. Don't count the citizens
            if(count($status) === 4 && in_array(0 , $status) !== false) {
                $this->startAdventure();
            }
        }
        public function newAdventure($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set new adventure
            $POST = json_decode($POST['JSON_data'], true);
            $difficulty = $POST['difficulty'];
            $location = $POST['location'];
            $other_invite = $POST['other_invite'];
            
            if(strlen($difficulty) < 1 || strlen($location) < 1) {
                $this->response->addTo("errorGameMessage" , "Please fill out all required inputs in the form");
                return false;
            }
            
            $param_username = $this->username;
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_INT);
            $stmt->execute();
            $adventure_id = $stmt->fetch(PDO::FETCH_ASSOC)->adventure_id;
            // Check if there is player is part of another adventure
            if($adventure_id != 0) {
                $this->response->addTo("errorGameMessage" , "Finish your current adventure before starting a new one");
                return false;
            }
            
            $difficulties = array("easy" => 1.0, "medium" => 5.0, "hard" => 12);
            if($difficulties[$_POST['difficulty']] > $_SESSION['gamedata']['adventurer_respect']) {
                $this->response->addTo("errorGameMessage" , "Adventurer respect too low for this difficulty");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();

                $this->advRequirements($this->session['profiency'], $this->adventure_data['difficulty'], 
                    $this->adventure_data['adventure_id']);
                $param_adventure_leader = $this->username;
                $param_difficulty = $difficulty;
                $param_location = $location;
                $param_profiency = $this->username;
                $param_other_invite = $other_invite;
                $sql = "INSERT INTO adventures (adventure_leader, difficulty, location, {$this->session['profiency']}, other_invite)
                        VALUES (:adventure_leader, :difficulty, :location, :profiency, :other_invite)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt->bindParam(":other_invite", $param_other_invite, PDO::PARAM_INT);
                $stmt->execute();
                $this->adventure_data['adventure_id'] = $this->db->conn->lastInsertId();
                
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_username = $this->username;
                $sql2 = "UPDATE adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage" , "Adventure started");
        }   
        public function advRequirements($role, $difficulty, $id, $username = false) {
            if(empty($role) || empty($difficulty) || empty($id)) {
                return false;
            }
            $id = intval($id);
            $username = ($username === false) ? $this->username : $username;
            $requirements = array();
            
            $adventure_req = array();
            $adventure_req['trader'] = array(
                "easy" => array("base" => 2, "rand" => "0/0"), 
                "medium" => array("base" => 3, "rand" => "0/0"), 
                "hard" => array("base" => 4, "rand" => "0/0"));
            $adventure_req['item_amount'] = array(
                "easy" => array("easy" => rand(4,5)),
                "medium" => array("easy" => rand(6,10), "medium" => 3),
                "hard" => array("easy" => rand(11,15), "medium" => rand(6, 9), "hard" => rand(3,4)));
             
            $adventure_req['warrior_amount'] = array("easy" => "3/4", "medium" => "6/8", "hard" => "10/15");
            $adventure_req['warrior'] = array("melee", "ranged");
            
            if($role == 'trader') {
                $rand = explode("/", $adventure_req['trader'][$difficulty]['rand']);
                $amount = $adventure_req['trader'][$difficulty]['base'] + round(rand($rand[0], $rand[1]) / 10, 1);
                $requirements[] = array("required" => "diplomacy", 'amount' => $amount);
            }
            else if($role == 'warrior') {
                $rand = explode("/", $adventure_req['warrior_amount'][$this->adventure_data['difficulty']]);
                $rand = rand($rand[0], $rand[1]);
                for($i = 0; $i < $rand; $i++ ) {
                    $requirements[] = $adventure_req['warrior'][array_rand($adventure_req['warrior'])];
                }
                $counts = array_count_values($requirements);
                foreach($counts as $key => $value) {
                    $requirements[] = array("requirement" => $key . ' warrior', "amount" => $value);
                }
            }
            else {
                //If $role is warrior or miner
                //Query to get the item requirements
                $param_role = $role;
                $param_difficulty = $difficulty;

                $sql = "SELECT name FROM items
                        WHERE adventure_requirement=1 AND 
                        adventure_requirement_role=:role AND 
                        adventure_requirement_difficulty=:difficulty
                ORDER BY rand() LIMIT 3";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                $stmt->execute();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $required_amount =  $adventure_req['item_amount'][$difficulty]['easy'];
                    array_push($requirements, array("required" => $row['name'], "amount" => $required_amount));
                }
                if(in_array($difficulty, array("medium", "hard"))) {
                    $sql = "SELECT name FROM items
                    WHERE adventure_requirement=1 AND 
                    adventure_requirement_role=:role 
                    AND adventure_requirement_difficulty=:difficulty
                    ORDER BY rand() LIMIT 2";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $required_amount =  $adventure_req['item_amount'][$difficulty]['medium'];
                        array_push($requirements, array("required" => $row['name'], "amount" => $required_amount));
                    }
                }
                if($difficulty === "hard") {
                    $sql = "SELECT name FROM items
                    WHERE adventure_requirement=1 AND 
                    adventure_requirement_role=:role AND 
                    adventure_requirement_difficulty=:difficulty
                    ORDER BY rand() LIMIT 2";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                    $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_INT);
                    $stmt->execute();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $required_amount =  $adventure_req['item_amount'][$difficulty]['hard'];
                        array_push($requirements, array("required" => $row['name'], "amount" => $required_amount));
                    }
                }
            }
            $param_username = $username;
            $param_adventure_id = $id;
            $param_role = $role;
            $param_required = "";
            $param_amount = "";
            if($username === 'citizen') {
                $param_status = 1;
                $sql = "INSERT INTO adventure_requirements (adventure_id, username, role, required, amount, provided, status)
                VALUES (:adventure_id, :username, :role, :required, :amount, :provided, :status)";

            } else {
                $sql = "INSERT INTO adventure_requirements (adventure_id, username, role, required, amount)
                VALUES (:adventure_id, :username, :role, :required, :amount)";
            }
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
            if($username === 'citizen') {
                $stmt->bindParam(":provided", $param_amount, PDO::PARAM_INT);
                $stmt->bindParam(":status", $param_status, PDO::PARAM_INT);
            }
            foreach($requirements as $key) {
                $param_required = $key['required'];
                $param_amount = $key['amount'];
                $stmt->execute();
            }
        }   
        public function provide($POST) {
            $item = $POST['item'];
            $quantity = $POST['quantity'];
            $warrior_check = $POST['warrior_check'];
            $route = ($warrior_check === true) ? "warrior" : "item"; 
            if($this->session['profiency'] == 'trader') {
                $this->response->addTo("errorGameMessage" , "The trader doesn't have to provide with anything");
                return false;
            }
            $profiences = array('farmer', 'miner', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                $this->response->addTo("errorGameMessage" , "Unexpected error");
            }
            
            $param_username = $this->username;
            $sql = "SELECT adventure_id, farmer, miner, trader, warrior, difficulty, location FROM adventures
                    WHERE :username IN (farmer, miner, warrior)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage" , "You are not in an adventure!");
                return false;
            }
            $this->adventure_data = $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $row['role'] = array_search($this->username, $row);
            
            if($route === 'warrior') {
                $queryArray = explode(",", $warrior_check);
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                $sql = "SELECT warrior_id, type FROM warriors WHERE warrior_id IN ($in) AND fetch_report=0 AND mission=0 AND username=?";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($queryArray);
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage" , "One or more of your warriors are not ready to fight!");
                    return false;
                }
                $row3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $item = $row3[0]['type'] . ' warrior';
            }
        
            $param_adventure_id = $row['adventure_id'];
            $param_role = $row['role'];
            $param_required = $item;
            $sql = "SELECT required, amount, provided
                    FROM adventure_requirements WHERE adventure_id=:adventure_id AND role=:role AND required=:required";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage" , "You are trying to provide the wrong item");
                return false;
            }
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row2['provided'] == $row2['amount']) {
                $this->response->addTo("errorGameMessage" , "You have provided enough!");
                return false;
            }
            if($route === 'item') {
                $amount = get_item($this->session['inventory'], $item)['amount'];
                if($amount < $quantity) {
                    $this->response->addTo("errorGameMessage" , "Not enough in inventory!");
                    return false;
                }
            }     
            else if ($route === 'warrior') {
                $warrior_type = array("melee" => 0, "ranged" => 0);
                foreach($row3 as $key) {
                    $warrior_type[$key['type']] += 1;
                }
                if(in_array(0, $warrior_type) == false) {
                    $this->response->addTo("errorGameMessage" , "One of the selected warriors is a different type");
                    return false;
                }
                $quantity = count($row3);
            }
            
            $missing_contribution = $row2['amount'] - $row2['provided']; 
            $missing_contribution = $missing_contribution - $quantity;
            ($missing_contribution == 0)? $status = 1: $status = 0;
            try {
                $this->db->conn->beginTransaction(); 
                
                $param_provided = $quantity + $row2['provided'];
                $param_status = $status;
                $param_adventure_id = $row['adventure_id'];
                $param_role = $row['role'];
                $param_required = $item;
                $sql = "UPDATE adventure_requirements SET provided=:provided, status=:status
                        WHERE adventure_id=:adventure_id AND role=:role AND required=:required";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":provided", $param_provided, PDO::PARAM_STR);
                $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
                $stmt->execute();
                
                if($route === 'item') {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($item, -$quantity, true);
                }
                else if ($route === 'warrior') {
                    $sql2 = "UPDATE warriors SET mission=2 WHERE warrior_id IN ($in) AND username=?";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->execute($queryArray);
                }
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            
            if($route == 'item') {
                $string = "You have provided %d of %s";
                $this->response->addTo("gameMessage" , sprintf($string, $quantity, $item));
            }
            else {
                $string = "You have provided %s warriors";
                $this->response->addTo("gameMessage" , sprintf($string, $quantity));
            }
            $sql = "SELECT role, required, amount, provided, status FROM adventure_requirements WHERE adventure_id=:adventure_id AND
            role IN ('farmer', 'miner', 'trader', 'warrior')
                    ORDER BY role ASC";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $row['adventure_id'];
            $stmt->execute();
            $requirements = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $trader_requirement = array_filter($requirements, function ($key) {
                        return ($key['role'] === 'trader');
                    });
            if(count($trader_requirement) > 0) {
                $location = $row['location'];
                $sql = "SELECT {$location} FROM diplomacy WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $row['trader'];
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $data['trader_diplomacy'] = $row[0];
                
                $trader_index = array_keys($trader_requirement);
                $trader_requirement = array_values($trader_requirement);
                $trader_requirement[0]['provided'] = $data['trader_diplomacy'];
                $data['requirements'][$trader_index[0]] = $trader_requirement[0];
            }
            $status = array_column($row2, 'status');
            // If there is 4 entries of 1 in the array everyone has provided enough
            if(count($status) === 4 && in_array(0 , $status) !== false) {
                $this->startAdventure();
            }

            ob_start();
            get_template('requirements', $requirements, true);
            $this->response->addTo("html", ob_get_clean());
            if($route === 'warrior') {
                $sql = "SELECT w.warrior_id, w.type, wl.stamina_level, wl.technique_level, wl.precision_level, wl.strength_level
                        FROM warriors as w
                        INNER JOIN warriors_levels as wl ON wl.warrior_id = w.warrior_id AND wl.username = w.username
                        WHERE w.mission=0 AND w.training_type='none' AND w.username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $data['warriors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ob_start();
                get_template('warrior_adventure', $data['warriors'], true);
                $this->response->addTo("html", ob_get_clean());
            }
            
            $this->db->closeConn();
        }
        public function hireCitizen($POST) {
            $role = strtolower($POST['role']);
            if($role === "warrrior") {
                $this->response->addTo("errorGameMessage", "Citizens can't be hired for the warrior role");
                return false;
            }
            $param_username = $this->username;
            $sql = "SELECT adventure_id, farmer, miner, warrior, trader, difficulty 
                    FROM adventures WHERE adventure_id=(SELECT adventure_id FROM adventure WHERE username=:username)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $this->adventure_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You are not currently in an adventure");
                return false;
            }
            if($row[$role] !== "none") {
                $this->response->addTo("errorGameMessage", "This role is already filled");
                return false;
            }

            if(6000 > $this->session['gold']) {
                $this->response->addTo("errorGameMessage", "Not enough gold in inventory");
                return false;
            } 
            try {
                $this->db->conn->beginTransaction();
                
                $param_adventure_id = intval($row['adventure_id']);
                $sql = "UPDATE adventures SET {$role}='citizen' WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt->execute();

                $this->UpdateGamedata->updateInventory('gold', -6000, true);
                
                $this->advRequirements($role, $row['difficulty'], $row['adventure_id'], 'citizen');
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->checkAdventureStatus();
            $this->response->addTo("gameMessage", "You have recruited a citizen for {$role}");
        }
        public function startAdventure() {
            $difficulty = $this->adventure_data['difficulty'];
            $adventure_type = array_values(array_filter(restore_file('adventures_times', true), function ($key) use ($difficulty) {
                return ($key['type'] == $difficulty);
            }))[0];
            $time = intval($adventure_type['time']);
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$time}seconds");
            $adventure_countdown = date_format($new_date, "Y-m-d H:i:s");

            $queryArray = array();
            array_push($queryArray, $this->adventure_data['farmer'], 
                        $this->adventure_data['miner'], 
                        $this->adventure_data['trader'], 
                        $this->adventure_data['warrior']);
            $in  = str_repeat('?,', count($queryArray) - 1) . '?';

            try {
                $this->db->conn->beginTransaction();
                
                $param_adventure_countdown = $adventure_countdown;
                $param_adventure_id = intval($this->adventure_data['adventure_id']);
                $sql = "UPDATE adventures SET adventure_countdown=:adventure_countdown, adventure_status=1
                        WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_countdown", $param_adventure_countdown, PDO::PARAM_INT);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->execute();
                
                $sql2 = "UPDATE adventure SET adventure_status=1 WHERE username IN ($in)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->execute($queryArray);

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $_SESSION['gamedata']['adventure_status'] = 1;
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Adventure started!");
        }
    }
