<?php
    class profile extends controller {
        public $data = array();
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Profile', true);
            $this->data = $this->model->getData();
            $this->post();
            $this->render('profile', 'Profile', false);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $postArray = $_POST;
                $result = $formhandler->checkData($postArray);
                if (array_search("Empty!", $result) !== false) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: There were one or more errors in your submission";
                    return false;
                }
                if(strlen($_POST['new_password']) < 5) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: Your password must be at least 5 characters";
                    return false;
                }
                if($_POST['new_password'] !== $_POST['confirm_password']) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: Your new password doesn't match";
                    return false;
                }
                else {
                    $this->bindData();
                }
            }
            else {
                return false;
            }
        }
        
        public function bindData() {
            $data = $this->model->getData();
            if(password_verify($_POST['current_password'], $data['password'])) {
                $this->model->changePassword($_POST['new_password']);
            }
            else {
                $_SESSION['gamedata']['log'][]  = "ERROR: The password you entered doesn't match";
            }
        }
    }
?>