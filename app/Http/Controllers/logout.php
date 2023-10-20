<?php
    class logout {
        private $session;
        
        function __construct($session) {
            $this->session = $session;
            $this->logout();
        }
        
        public function logout() {
            $this->session->destroy();
        }
    }
?>