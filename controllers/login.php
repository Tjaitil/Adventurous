<?php
    class login extends controller {
        public $error = array('userErr' => '', 'passErr' => '', 'loginfail' => '');
        private $username;
        private $password;
        public $session;
        
        function __construct ($session) {
            parent::__construct(false);
            if(isset($_SESSION['username'])) {
                header("Location: /main");
                exit();
            }
            $this->session = $session;
            if(isset($_POST['username'])) {
                $this->checkData();
            }
        }
        public function index() {
            $array = array(NULL, 'none');
            /*if (isset($_SESSION['gamedata'])) {
                    header("Location: /main");
            }*/
            // Return site with a error array
            $this->renderWE('login', 'Login', false, $this->error);
        }
        public function checkData () {
            if(empty($_POST["username"])) {
                $this->error['userErr'] = "Please enter username!";
            }
            else {
                $this->username = trim($_POST['username']);
            }
            // Check if password is empty
            if(empty(trim($_POST['password']))){
                $this->error['passErr'] = 'Please enter your password.';
            }
            else {
                $this->password = $_POST['password'];
             }
            if(!empty($this->error['userErr']) || !empty($this->error['passErr'])) {
                return $this->error;
            }
            if(empty($this->error['userErr']) || empty($this->error['passErr'])) {
                $this->loadModel('Login', false);
                $checkuser = $this->model->checkuser($this->username, $this->password);
                if ($this->model->status == false) {
                    $this->error['loginfail'] = "Something went wrong, please try again later";
                }
                else if (!$this->model->status == false) {
                    if (password_verify($this->password, $this->model->row['password'])) {
                        $this->session->setSession($this->username, $loggedin = true);
                        if($this->model->profiency != "none") {
                            // If profiency is set redirect to main
                           header('Location: /main');
                           exit();
                        }
                        else {
                            // If the user has no profiency it is a new user and needs to chose one
                            header('Location: /newuser');
                            exit();
                        }
                    }
                    else {
                        $this->error['passErr'] = "The password you submitted doesn't match";
                    }
                }
                
            }
            else {
                $this->error['loginfail'] = "Something went wrong, please try again later";
            }
        }
    }
?>