<?php
    class Profile_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $sql = "SELECT password FROM users WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        public function changePassword($new_password) {
        
            $sql = "UPDATE users SET password=:password WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":password", $param_password, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_username = $this->username;
            $stmt->execute();
            if($stmt->rowCount() > 0) {
                $_SESSION['gamedata']['log'][] = "Your password has been changed!";
            }
            else {
                $_SESSION['gamedata']['log'][] = "ERROR: Something unexpected happened please try again!";
                //ERROR LOG
            }
        }
    }
?>