<?php
    class setadventure_model extends model {
        public $username;
        public $session;
        public $adventure_data;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
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
                $this->conn->beginTransaction();
                $sql = "INSERT INTO adventures (adventure_leader) VALUES (:adventure_leader)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_leader", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $this->adventure_data['adventure_id'] = $this->conn->lastInsertId();
                
                $sql2 = "UPDATE adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_username = $this->username;
                $stmt2->execute();
                
                $sql3 = "UPDATE adventures SET difficulty=:difficulty, location=:location, 
                         {$this->session['profiency']}=:profiency WHERE adventure_id=:adventure_id AND
                         adventure_leader=:adventure_leader";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt3->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt3->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt3->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt3->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                $param_difficulty = $this->adventure_data['difficulty'];
                $param_location = $this->adventure_data['location'];
                $param_profiency = $this->username;
                $param_adventure_id = $this->adventure_data['adventure_id'];
                $param_adventure_leader = $this->username;
                $stmt3->execute();
                
                $sql4 = "INSERT INTO adventures_{$this->session['profiency']} (adventure_id, username)
                         VALUES(:adventure_id, :username)";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                // $param_adventure_id is already defined
                $param_username = $this->username;
                $stmt4->execute();
                
                $this->advRequirments($this->session['profiency'], $this->adventure_data['difficulty']);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            $this->closeConn();
        }
        
        private function advRequirments($role, $difficulty) {    
            $difficulties = array();
            $difficulties['easy'] = array("grade" => 1, "multiplier" => 1);
            $difficulties['medium'] = array("grade" => 2, "multiplier" => 1.3);
            $difficulties['hard'] = array("grade" => 3, "multiplier" => 1.5);
            
            $requirments_amount = 2;
            $adventure_req = array();
            $adventure_req['trader'] = array("easy" => 2/3, "medium" => 4/6, "hard" => 8/10);
            $adventure_req['item_amount'] = array("easy" => 3, "medium" => 4, "hard" => 5);
            $adventure_req['warrior'] = array("melee", "ranged");
            $requirments = array();
            
            
            if($role == 'trader') {

                $rand = explode("/", $adventure_req['trader'][$difficulty]);
                $requirments[] = array("requirement" => "diplomacy", 'amount' => rand($rand[0], $rand[1]));
            }
            else if($role == 'warrior') {
                for($i = 0; $i < count($requirments_amount); $i++ ) {
                    if($difficulty_check)
                    $requirments[] = array_rand($adventure_req[$role]);
                }
                $counts = array_count_values($requirments);
                $requirments = array();
                foreach($counts as $key => $value) {
                    $requirments[] = array("requirement" => $value, "amount" => $key);
                }
            }
            else {
                //If $role is warrior or miner
                //Query to get the item requirements
                $count = $adventure_req['item_amount'][$difficulty] - 1;
            
                $sql = "SELECT name FROM adventure_req_items WHERE role=:role AND difficulty=:difficulty
                    ORDER BY rand() LIMIT 1;";
                $stmt = $this->conn->prepare($sql);
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
                $stmt = $this->conn->prepare($sql);
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
            
            $sql = "INSERT INTO adventure_requirements (adventure_id, username, role, location, required, amount)
            VALUES (:adventure_id, :username, :role, :location, :required, :amount)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":required", $param_required, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
            $param_username = $this->username;
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $param_role = $this->session['profiency'];
            $param_location = $this->adventure_data['location'];
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
                var_dump($stmt->rowCount());
            }
        }   
        public function provide($adventure_id, $route, $item, $quantity, $warrior_check) {
            if($this->session['profiency'] == 'trader') {
                $this->gameMessage("The trader role doesn't have to provide with anything");
                return false;
            }
            $profiences = array('farmer', 'miner', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                $this->gameMessage("ERROR");
                return false;
            }
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['adventure_id'] != $adventure_id) {
                $this->gameMessage("ERROR: This adventure doesn't exists!");
                return false;
            }
            $sql = "SELECT adventure_id, difficulty, location FROM adventures WHERE adventure_id=adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT required, amount FROM adventure_requirments WHERE difficulty=:difficulty AND location=:location AND role=:role";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $param_difficulty = $row['difficulty'];
            $param_location = $row['location'];
            $param_role = $this->session['profiency'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT provided FROM adventures_" . $this->session['profiency'] . "
                       WHERE adventure_id=:adventure_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $param_username = $this->username;
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                
            if($row3['provided'] == $row2['amount']) {
                $this->gameMessage("You have provided enough!");
                return false;
            }
            
            if($route == 'item') {
                if($row2['required'] != $item) {
                $this->gameMessage("ERROR: You are trying to provide the wrong item");
                return false;
                }
            
                $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $this->username;
                $stmt->execute();
                $row4 = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row4['amount'] < $quantity) {
                    print "ERROR, not enough in inventory!";
                    return false;
                }
            }     
            else if ($route == 'warrior') {
                $queryArray = explode(",", $warrior_check);
                if(count($queryArray) > $row2['amount']) {
                    $this->gameMessage("You are trying to provide more warriors than you have to");
                    return false;
                }
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                $sql = "SELECT warrior_id FROM warriors WHERE warrior_id IN ($in) AND fetch_report=0 AND username=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($queryArray);
                $row4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("Check again if your soldiers are active or not!");
                    return false;
                }
                $quantity = count($row4);
                $row4['amount'] = count($row4);
            }
            
            $missing_contribution = $row2['amount'] - $row3['provided']; 
            if($missing_contribution < $quantity) {
                $quantity = $missing_contribution; //If the player is trying to provide more than is requires
            }
            $missing_contribution = $missing_contribution - $quantity;
            ($missing_contribution == 0)? $status = 1: $status = 0;
            try {
                $this->conn->beginTransaction(); 
                
                $sql = "UPDATE adventures_" . $this->session['profiency'] . " SET provided=:provided, status=:status
                        WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":provided", $param_provided, PDO::PARAM_STR);
                $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_provided = $quantity + $row3['provided'];
                $param_status = $status;
                $param_adventure_id = $adventure_id;
                $param_username = $this->username;
                $stmt->execute();
                if($route == 'item') {
                    require_once('../' . constant("ROUTE_HELPER") . 'update_inventory.php');
                    update_inventory($this->conn, $this->username, $item, -$quantity);
                }
                else if ($route == 'warrior') {
                    $sql2 = "UPDATE warriors SET mission=1 WHERE warrior_id IN ($in) AND username=?";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->execute($queryArray);
                }
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $string = "You have provided %s";
            $this->gameMessage(sprintf($string, $quantity), true);
            js_echo(array($this->session['profiency'], $param_provided, $missing_contribution, $status));
            $data = get_inventory($this->conn, $this->username);
            js_foreach($data);
        }
    }
?>