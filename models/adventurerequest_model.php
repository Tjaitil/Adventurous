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
            
            $sql = "SELECT adventure_id, " . $row['role'] .  " FROM adventures WHERE adventure_id=:adventure_id";
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
            
                $sql = "UPDATE adventures SET " . $row['role'] . "=:username WHERE adventure_id=:adventure_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $row['adventure_id'];
                //$param_username is already defined;
                $stmt->execute();
                
                $sql2 = "INSERT INTO adventures_" . $row['role'] . " (adventure_id, username, provided)
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
    }
?>