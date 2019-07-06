<?php
    class registration extends controller {
        public $error = array('usernameErr' => '', 'emailErr' => '', 'passwordErr' => '', 'password_confirmErr' => '',
                              'countryErr' => '', 'dobErr' => '', 'checkboxErr' => '');
        public $userData = array();
        private $POST_array;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('registration', false);
            $this->post();
            $this->renderWE('registration', 'Registration', false, $this->error);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                $this->POST_array = $_POST;
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $result = $formhandler->checkData($this->POST_array);
                if (array_search("Empty!", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value;
                        }
                    return;
                }
                $errors = $this->model->check($this->POST_array);
                if(count($errors) > 0) {
                    $this->error = array_merge($this->error, $errors);
                }
                else {
                    $this->bindData();
                }
            }
            else {
                return false;
            }
        }
        
        public function bindData () {
                $this->userData['username'] = trim($_POST['username']);
                $this->userData['email'] = $this->POST_array['email'];
                $this->userData['password'] = $this->POST_array['password'];
                $this->userData['country'] = $this->POST_array['country'];
                $this->userData['dob'] = $this->POST_array['dob'];
                $insert = $this->model->insertDb($this->userData);
                if($insert !== false) {
                    header("Location: /login");
                    exit();
                }
                else {
                    echo "<script> alert('Something wrong happened, please try again later') </script>";
                }
        }
    }
?>