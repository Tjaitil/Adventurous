<?php
    class messages_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getMessages() {
            $data = array();
            $sql = "SELECT id, title, sender as receiver, date, message_read FROM messages
                    WHERE receiver=:username ORDER BY date DESC LIMIT 8";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['inbox'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT id, title, receiver, date, message_read FROM messages
                    WHERE sender=:username ORDER BY date DESC LIMIT 8";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['sent'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $data;
        }
        public function showMessage($message_id) {
            $sql = "SELECT message_read, title, sender, date, message FROM messages WHERE id=:id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $param_id = $message_id;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['message_read'] == 0) {
                $sql = "UPDATE messages SET message_read=1 WHERE id=:id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
                $param_id = $message_id;
                $stmt->execute();
            }
            $this->db->closeConn();
            js_echo($row);
        }
        public function sendMessage($messageData) {
            $sql = "INSERT INTO messages (title, sender, receiver, message) VALUES(:title, :sender, :receiver, :message)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":title", $param_title, PDO::PARAM_STR);
            $stmt->bindParam(":sender", $param_sender, PDO::PARAM_STR);
            $stmt->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
            $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
            $param_title = $messageData['title'];
            $param_sender = $this->username;
            $param_receiver = $messageData['receiver'];
            $param_message = $messageData['message'];
            $stmt->execute();
            $this->db->closeConn();
        }
        public function userCheck($username, $js = false) {
            $sql = "SELECT username FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            if($js == true) {
               echo $stmt->rowCount(); 
            }
            else {
                return $stmt->rowCount();
            }
        }
        public function getmMessages($table, $type, $date) {
            // AJAX function to get the next or previous messages from both inbox and sent tables
            switch($table) {
                case 'inbox':
                    if(strpos($type,'Next')) {
                        $sql = "SELECT id, title, sender, receiver, date, message_read FROM messages
                                WHERE receiver=:username AND date < :date ORDER BY date DESC LIMIT 8";      
                    }
                    else {
                        $sql = "SELECT id, title, sender, receiver, date, message_read FROM messages
                                WHERE receiver=:username AND date > :date ORDER BY date ASC LIMIT 8";
                    }
                    break;
                case 'sent':
                    if(strpos($type,'Next')) {
                        $sql = "SELECT id, title, receiver, date, message_read FROM messages
                                WHERE sender=:username AND date > :date ORDER BY date DESC LIMIT 8";      
                    }
                    else {
                        $sql = "SELECT id, title, sender, receiver, date, message_read FROM messages
                                WHERE sender=:username AND date > :date ORDER BY date ASC LIMIT 8";
                    }
                    break;
                case 'default':
                        return false;
                    break;
            }
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":date", $param_date, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_date = $date;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                return false;   
            }
            else if($stmt->rowCount() < 8) {
                echo "next_false";
            }
            echo "#";
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            get_template("messages", $row, true);
        }
        public function checkMessages() {
            $sql = "SELECT COUNT(message_read) FROM messages WHERE message_read = 0 AND receiver=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo $row['COUNT(message_read)'];
        }
    }
?>