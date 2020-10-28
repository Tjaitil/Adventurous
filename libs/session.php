<?php
    class session {
        public $status = 'neutral';
        
        function __construct () {
            session_start();
        }
        public function setSession($username, $loggedin) {
            $_SESSION['username'] = $username;
            $_SESSION['loggedin'] = true;
            $_SESSION['session_id'] = $this->generateID();
            $_SESSION['profiency'] = true;
            $this->status = true;
        }
        public function validateLogin() {
            if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
                $this->status = false;
            }
        }
        private function generateID() {
            $id = null;
            for($i = 0; $i < 6; $i++) {
                $id .= rand(1,9);
            }
            return $id;
        }
        public function destroy() {
            session_unset();
            session_destroy();
            header("Location: /login");
            exit;
        }
    }
?>