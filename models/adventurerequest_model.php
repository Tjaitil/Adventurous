<?php
    class AdventureRequest_model extends model {
        public $username;
        public $session;
        public $adventure_data;
        public $joiner;
        
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            // Assign the database class to db property;
        }
        public function request($adventure_id, $route, $invitee = false) {
            //Send request or invite to join adventure
            if(in_array($route, array("invite", "request")) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            if($route == 'request') {
                $sql = "SELECT adventure_leader FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $param_adventure_id = $adventure_id;
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: This adventure is no longer available!", true);
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else if($route == 'invite') {
                $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = strtolower($invitee);
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("ERROR: The user you have invited doesn't exists", true);
                    return false;
                }
                
                if($stmt->fetch(PDO::FETCH_OBJ)->adventure_id != 0) {
                    $this->gameMessage("ERROR: The person you have invite is already in an adventure", true);
                    return false;
                }
            }
            
            $sql = "SELECT sender, receiver, adventure_id FROM adventure_requests
                    WHERE adventure_id=:adventure_id AND (sender =:username1 OR receiver =:username1)
                    AND (sender =:username2 OR receiver =:username2)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->bindParam(":username1", $param_username1, PDO::PARAM_STR);
            $stmt->bindParam(":username2", $param_username2, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            ($route == 'request')? $param_username1 = $row['adventure_leader'] : $param_username1 = $invitee; 
            $param_username2 = $this->username;
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You have already sent a {$route}", true);
                return false;
            }
            
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            ($route  == 'request')? $param_username = $this->username : $param_username = $invitee;
            $stmt->execute();
            $role = $stmt->fetch(PDO::FETCH_OBJ)->profiency;
            
            $sql = "SELECT farmer, miner, trader, warrior FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2[$role] !== 'none') {
                $this->gameMessage("ERROR: The role has already been filled!", true);
                return false;
            }
            
            require('../' . constant('ROUTE_HELPER') . 'message_templates.php');
            switch($route) {
                case 'request':
                    $title = "Adventure request:";
                    $text = $message_templates['adventure_request'];
                    $receiver = $row['adventure_leader'];
                    break;
                case 'invite':
                    $title = "Adventure invite:";
                    $text = $message_templates['adventure_invite'];
                    $receiver = $invitee;
                    break;
            }
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "INSERT INTO messages (title, sender, receiver, message) VALUES (:title, :sender, :receiver, :message)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
                $stmt->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                $stmt->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
                $param_title = $title;
                $param_sender = $this->username;
                $param_receiver = $receiver;
                $param_message = $text;
                $stmt->execute();
                
                $sql2 = "INSERT INTO adventure_requests (sender, receiver, adventure_id, role, method)
                         VALUES(:sender, :receiver, :adventure_id, :role, :method)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                $stmt2->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt2->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt2->bindParam(":method", $param_method, PDO::PARAM_STR);
                $param_receiver = $receiver;
                $param_sender = $this->username;
                $param_adventure_id = $adventure_id;
                $param_role = $role;
                $param_method = $route;
                $stmt2->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            unset($stmt, $stmt2);
            unset($this->db->conn);
            if($route == 'invite') {
                $this->gameMessage("Invite sent to ". ucfirst($invitee), true);
            }
            else {
                $this->gameMessage("Request sent to ". ucfirst($row['adventure_leader']), true);
            }
        }
        public function joinAdventure($request_id) {
            $sql = "SELECT sender, receiver, adventure_id, method, role FROM adventure_requests WHERE request_id=:request_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
            $param_request_id = $request_id;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again", false);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['method'] == 'request') {
                $this->joiner = $row['sender'];
                $receiver = $row['receiver'];
            }
            else {
                $this->joiner = $row['receiver'];
                $receiver = $row['sender'];
            }

            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->joiner;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['adventure_id'] != 0) {
                if($row['method'] == 'request') {
                    $this->gameMessage("ERROR: You are already in an adventure", true);   
                }
                else {
                    $this->gameMessage("ERROR: The person you have invited is already in an adventure", true);
                }
                return false;
            }
        
            $sql = "SELECT adventure_id, difficulty, {$row['role']} FROM adventures
                    WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $param_adventure_id = $row['adventure_id'];
            $stmt->execute();
            $this->adventure_data = $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: Adventure doesn't exists", true);
                return false;
            }
            else if($row3[$row['role']] != 'none') {
                $this->gameMessage("ERROR: Someone already has that role!", true);
                return false;
            }
            
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['role'] != $row2['profiency']) {
                $this->gameMessage("ERROR: Profiency doesn't match the role in adventure", true);
                return false;
            }
            
            $profiences = array("farmer", "miner", "trader", "warrior");
            if(in_array($row['role'], $profiences) == false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                $sql = "UPDATE adventures SET {$row['role']}=:username WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $row['adventure_id'];
                $param_username = $this->joiner;
                $stmt->execute();
                
                $sql3 = "DELETE FROM adventure_requests WHERE request_id=:request_id";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
                $param_request_id = $request_id;
                $stmt3->execute();
                
                $sql4 = "UPDATE adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username and $param_adventure_id is already defined
                $stmt4->execute();
                
                $sql5 = "INSERT INTO messages (title, sender, receiver, message) VALUES(:title, :sender, :receiver, :message)";
                $stmt5 = $this->db->conn->prepare($sql5);
                $stmt5->bindParam(":title", $param_title, PDO::PARAM_STR);
                $stmt5->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                $stmt5->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                $stmt5->bindParam(":message", $param_message, PDO::PARAM_STR);
                $param_title = "Adventure update!";
                $param_sender = "game_info@adventurous";
                $param_receiver = $receiver;
                $param_message = nl2br("For your information {$this->joiner} has joined your adventure!");
                $stmt5->execute();
                
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
            
            $this->gameMessage(ucfirst($this->joiner) ." has joined the adventure!", true);
        }
        private function advRequirements() {
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->joiner;
            $stmt->execute();
            $role = $stmt->fetch(PDO::FETCH_OBJ)->profiency;
            
            $difficulty = $this->adventure_data['difficulty'];
            $difficulties = array();
            $difficulties['easy'] = array("grade" => 1, "multiplier" => 1);
            $difficulties['medium'] = array("grade" => 2, "multiplier" => 1.3);
            $difficulties['hard'] = array("grade" => 3, "multiplier" => 1.5);
            
            $requirments_amount = 2;
            $adventure_req = array();
            $adventure_req['trader'] = array("easy" => "2.00/3.00", "medium" => "4.00/6.00", "hard" => "8.00/10.00");
            $adventure_req['item_amount'] = array("easy" => 3, "medium" => 4, "hard" => 5);
            $adventure_req['warrior_amount'] = array("easy" => "2/3", "medium" => "4/8", "hard" => "10/15");
            $adventure_req['warrior'] = array("melee", "ranged");
            $requirements = array();
            
            if($role == 'trader') {
                $rand = explode("/", $adventure_req['trader'][$difficulty]);
                $requirements[] = array("requirement" => "diplomacy", 'amount' => rand($rand[0], $rand[1]));
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
            $param_username = strtolower($this->joiner);
            $param_adventure_id = $this->adventure_data['adventure_id'];
            $param_role = $role;
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
    }
?>