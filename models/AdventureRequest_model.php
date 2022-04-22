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
        public function request($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from adventure.js
            // Function to either request to join an adventure or invite a player to adventure
            
            $adventure_id = $POST['id'];
            $route = $POST['route'];
            $invitee = $POST['invitee'];

            if(in_array($route, array("invite", "request")) === false) {
                $this->response->addTo("errorGameMessage", "Something unexpected happened, please try again");
                return false;
            }
            $difficulties = array("easy" => 1.0, "medium" => 5.0, "hard" => 12);
            
            if($route == 'request') {
                $param_adventure_id = $adventure_id;
                $sql = "SELECT adventure_leader, difficulty, other_invite FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage", "This adventure is no longer available!");
                    return false;
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // if($row['other_invite'] == 0) {
                //     $this->response->addTo("errorGameMessage", "Invite only is on for this adventure");
                //     return false;   
                // }
                if($difficulties[$row['difficulty']] > $this->session['adventurer']) {
                    $this->response->addTo("errorGameMessage", "You don't have high enough adventure_respect to join this adventure!");
                    return false;
                }
            }
            else if($route == 'invite') {
                $param_username = $this->username;
                $sql = "SELECT adventure_leader, difficulty, other_invite FROM adventures WHERE adventure_leader=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // if($row['other_invite'] == 0 && $row['adventure_leader'] != $this->username) {
                //     $this->response->addTo("errorGameMessage", "Invite only is on for this adventure");
                //     return false;
                // }
                
                $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = strtolower($invitee);
                $stmt->execute();
                if(!$stmt->rowCount() > 0) {
                    $this->response->addTo("errorGameMessage", "The user you have invited doesn't exists");
                    return false;
                }
                
                if($stmt->fetch(PDO::FETCH_OBJ)->adventure_id != 0) {
                    $this->response->addTo("errorGameMessage", "The person you have invite is already in an adventur");
                    return false;
                }
                $sql = "SELECT adventurer_respect FROM user_levels WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = strtolower($invitee);
                $stmt->execute();
                $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

                if($difficulties[$row['difficulty']] > $row2['adventurer_respect']) {
                    $this->response->addTo("errorGameMessage", "The person you are trying to invite doesn't have high enough adventurer respect");
                    return false;
                }
            }
            
            $param_adventure_id = $adventure_id;
            ($route == 'request')? $param_username1 = $row['adventure_leader'] : $param_username1 = $invitee; 
            $param_username2 = $this->username;
            $sql = "SELECT sender, receiver, adventure_id FROM adventure_requests
                    WHERE adventure_id=:adventure_id AND (sender =:username1 OR receiver =:username1)
                    AND (sender =:username2 OR receiver =:username2)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->bindParam(":username1", $param_username1, PDO::PARAM_STR);
            $stmt->bindParam(":username2", $param_username2, PDO::PARAM_STR);
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You have already sent a {$route}");
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
                $this->response->addTo("errorGameMessage", "The role has already been filled!");
                return false;
            }
            
            switch($route) {
                case 'request':
                    $receiver = $row['adventure_leader'];
                    break;
                case 'invite':
                    $receiver = $invitee;
                    break;
            }
            
            try {
                $this->db->conn->beginTransaction();
                
                // $param_title = $title;
                // $param_sender = $this->username;
                // $param_receiver = $receiver;
                // $param_message = $text;
                // $sql = "INSERT INTO messages (title, sender, receiver, message) VALUES (:title, :sender, :receiver, :message)";
                // $stmt = $this->db->conn->prepare($sql);
                // $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
                // $stmt->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                // $stmt->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                // $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
                // $stmt->execute();
                
                $param_receiver = $receiver;
                $param_sender = $this->username;
                $param_adventure_id = $adventure_id;
                $param_role = $role;
                $param_method = $route;
                $sql2 = "INSERT INTO adventure_requests (sender, receiver, adventure_id, role, method)
                         VALUES(:sender, :receiver, :adventure_id, :role, :method)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                $stmt2->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt2->bindParam(":role", $param_role, PDO::PARAM_STR);
                $stmt2->bindParam(":method", $param_method, PDO::PARAM_STR);
                $stmt2->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            if($route == 'invite') {
                $message = "Invite sent to ". ucfirst($invitee);
            }
            else {
                $message = "Request sent to ". ucfirst($row['adventure_leader']);
            }
            $this->response->addTo("gameMessage", $message);
        }
        public function joinAdventure($POST) {
            // $POST variable holds the post data
            $request_id = intval($POST['id']);
            $param_request_id = intval($request_id); 
            $sql = "SELECT sender, receiver, adventure_id, method, role FROM adventure_requests WHERE request_id=:request_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "Something unexpected happened, please try again");
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

            $param_username = $this->joiner;
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['adventure_id'] != 0) {
                if($row['method'] == 'request') {
                    $this->response->addTo("errorGameMessage", "You are already in an adventure");
                }
                else {
                    $this->response->addTo("errorGameMessage", "The person you have invited is already in an adventure");
                }
                return false;
            }
        
            $param_adventure_id = $row['adventure_id'];
            $sql = "SELECT adventure_id, difficulty, {$row['role']} FROM adventures
                    WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->execute();
            $this->adventure_data = $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "Adventure doesn't exists");
                return false;
            }
            else if($row3[$row['role']] != 'none') {
                $this->response->addTo("errorGameMessage", "Someone already has that role!");
                return false;
            }
            
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined;
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['role'] != $row2['profiency']) {
                $this->response->addTo("errorGameMessage", "Profiency doesn't match the role in adventure");
                return false;
            }
            $SetAdventure = $this->loadModel('SetAdventure');
            $profiences = array("farmer", "miner", "trader", "warrior");
            if(in_array($row['role'], $profiences) == false) {
                $this->response->addTo("errorGameMessage", "Something unexpected happened, please try again!");
                return false;
            }
            try {
                $this->db->conn->beginTransaction();
                $SetAdventure->advRequirements($row['role'], $this->adventure_data['difficulty'],
                                $row['adventure_id'], strtolower($this->joiner));
                                
                $sql2 = "UPDATE adventures SET {$row['role']}=:username WHERE adventure_id=:adventure_id";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $row['adventure_id'];
                $param_username = strtolower($this->joiner);
                $stmt2->execute();
                
                $sql3 = "DELETE FROM adventure_requests WHERE request_id=:request_id";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
                $param_request_id = $request_id;
                $stmt3->execute();
                
                $sql4 = "UPDATE adventure SET adventure_id=:adventure_id, notification=1 WHERE username=:username";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username and $param_adventure_id is already defined
                $stmt4->execute();
                

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            
            if($this->joiner == $this->username) {
                $message = "You have joined the adventure!";
            }
            else {
                $message = ucfirst($this->joiner) . " has joined the adventure!";
            }
            $this->response->addTo("gameMessage", $message);
        }
        public function declineRequest($POST) {
            $param_request_id = intval($POST['request_id']);
        
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE adventure_requests SET status='decline' WHERE request_id=:request_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":request_id", $param_request_id, PDO::PARAM_STR);
                $param_request_id = $param_request_id;
                $stmt->execute();

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
        }
    }
