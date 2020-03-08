<?php
    class session {
        public $status = 'neutral';
        
        function __construct () {
            session_start();
        }
        public function setSession($username, $loggedin) {
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            $_SESSION['profiency'] = true;
            $this->status = true;
        }
        public function validateLogin() {
            if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                $this->status = false;
            }
        }
        public function destroy() {
            session_unset();
            session_destroy();
            header("Location: /login");
            exit;
        }
    }
?>