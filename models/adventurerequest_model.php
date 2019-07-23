<?php
    class adventurerequest_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function adventureRequest($adventure_id, $route, $invitee = false) {
            $methods = array("invite", "request");
            if(array_search($route, $methods) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            if($route == 'request') {
                $sql = "SELECT adventure_leader FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $adventure_id;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("This adventure is no longer available!", true);
                }
            }
            
            $sql = "SELECT sender, receiver, adventure_id FROM adventure_requests
                    WHERE adventure_id=:adventure_id AND (sender OR receiver =:username1) AND (sender OR receiver =:username2)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username1", $param_username1, PDO::PARAM_STR);
            $stmt->bindParam(":username2", $param_username2, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            ($route == 'request')? $param_username1 = $row['adventure_leader'] : $param_username1 = $invitee; 
            $param_username2 = $this->username;
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $this->gameMessage("You have already sent a " . $route, true);
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
            
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            ($route  == 'request')? $param_username = $this->username : $param_username = $invitee;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $role = $row['profiency'];
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "INSERT INTO messages (title, sender, receiver, message) VALUES (:title, :sender, :receiver, :message)";
                $stmt = $this->conn->prepare($sql);
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
                $stmt2 = $this->conn->prepare($sql2);
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
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            unset($stmt, $stmt2);
            unset($this->conn);
        }
        
        public function joinAdventure($request_id) {
            $sql = "SELECT sender, receiver, adventure_id, method, role FROM adventure_requests WHERE request_id=:request_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
            $param_request_id = $request_id;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT adventure_id FROM adventures WHERE farmer OR miner OR trader OR warrior =:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            ($row['method'] == 'invite') ? $param_username = $row['receiver']: $param_username = $row['sender'];
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_ASSOC);
            if($stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: User is already in a adventure!", true);
                return false;
            }
            
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['role'] != $row2['profiency']) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            $sql = "SELECT adventure_id, difficulty, {$row['role']} FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $row['adventure_id'];
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_NUM);
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: Adventure does not exists!", true);
                return false;
            }
            if($row3[1] != 'none') {
                $this->gameMessage("ERROR: Someone already has that role!", true);
                return false;
            }
            
            $profiences = array("farmer", "miner", "trader", "warrior");
            if(in_array($row['role'], $profiences) == false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            try {
                $this->conn->beginTransaction();
            
                $sql = "UPDATE adventures SET {$row['role']}=:username WHERE adventure_id=:adventure_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $row['adventure_id'];
                //$param_username is already defined;
                $stmt->execute();
                
                $sql2 = "INSERT INTO adventures_{$row['role']} (adventure_id, username, provided)
                         VALUES(:adventure_id, :username, 0)";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_adventure_id is already defined;
                //$param_username is already defined;
                $stmt2->execute();
                
                $sql3 = "DELETE FROM adventure_requests WHERE request_id=:request_id";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
                $param_request_id = $request_id;
                $stmt3->execute();
                
                $sql4 = "UPDATE adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username and $param_adventure_id is already defined
                $stmt4->execute();
                $this->adventureRequest($row['role'], $row3['difficulty']);
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
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
                $requirments[] = array('amount' => rand($rand[0], $rand[1]), "location" => "");
            }
            else if($role == 'warrior') {
                for($i = 0; $i < count($requirments_amount); $i++ ) {
                    if($difficulty_check)
                    $requirments[] = array_rand($adventure_req[$role]);
                }
                $counts = array_count_values($requirments);
                $requirments = array();
                foreach($counts as $key => $value) {
                    $requirments[] = array("requirment" => $value, "amount" => $key);
                }
            }
            else {
                //If $role is trader or warrior
                //Query to get the item requirements
                                //If $role is trader or warrior
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
            
            $sql = "INSERT INTO adventure_requirments (required, amount) VALUES (:required, :amount)";
            $stmt = $this->conn($sql);
            foreach($requirments as $key) {
                $stmt->bindParam();
                $stmt->bindParam();
                $param_required = $key['requirment'];
                if(in_array($role, array("farmer", "miner")) === true) {
                    $rand = explode("/", $key['amount']);
                    $param_amount = rand($rand[0], $rand[1]);
                    ($key['difficulty'] < $difficulty_grade) ? round($param_amount*= $difficulties[$difficulty]['multiplier']) : "";
                }
                else {
                    $param_amount = $key['amount'];
                }
                $stmt->execute();
            }
        }
    }
?>