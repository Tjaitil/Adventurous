<?php
    class messages_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getMessages() {
            $data = array();
            $sql = "SELECT id, title, sender, receiver, date, message_read FROM messages WHERE receiver=:username ORDER BY date DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['inbox'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT id, title, sender, receiver date, message_read FROM messages WHERE sender=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['sent'] = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $data;
        }
        
        public function showMessage($message_id) {
            $sql = "SELECT message_read, title, sender, message FROM messages WHERE id=:id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $param_id = $message_id;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['message_read'] == 0) {
                $sql = "UPDATE messages SET message_read=1 WHERE id=:id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $param_id = $message_id;
                $stmt->execute();
            }
            $this->closeConn();
            js_echo($row);
        }
        
        public function sendMessage($messageData) {
            $sql = "INSERT INTO messages (title, sender, receiver, message) VALUES(:title, :sender, :receiver, :message)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":sender", $param_sender, PDO::PARAM_STR);
            $stmt->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
            $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
            $param_title = $messageData['title'];
            $param_sender = $this->username;
            $param_receiver = $messageData['receiver'];
            $param_message = $messageData['message'];
            $stmt->execute();
            $this->closeConn();
        }

        public function userCheck($username, $js = false) {
            $sql = "SELECT username FROM user_data WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            $this->closeConn();
            if($js == true) {
               echo $stmt->rowCount(); 
            }
            else {
                return $stmt->rowCount();
            }
        }
    }
?>