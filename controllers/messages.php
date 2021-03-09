<?php
    class messages extends controller {
        public $data = array();
        public $error = array('receiverErr' =>'', 'messageErr' => '');
        public $messageData = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Messages', true);
            $this->data = $this->model->getMessages();
            $this->loadModel('CanvasGameID', true);
            $this->model->checkSessionID();
            $this->post();
            $this->renderWE('messages', 'Messages', $this->data, $this->error);
        }
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $postArray = $_POST;
                $result = $formhandler->checkData($postArray);
                if (array_search("empty", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value; 
                        }
                }
                if(strlen($_POST['message']) < 2) {
                    $this->error['messageErr'] = "The message must be at least 2 charactes long!";
                }
                if($this->model->userCheck($_POST['receiver']) == 0 ) {
                    $this->error['receiverErr'] = "The user you are trying to message does not exists!";
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
            if(empty($_POST['title'])) {
                $this->messageData['title'] = "(No subject)";
            }
            else {
               $this->messageData['title'] = $_POST['title']; 
            }
            $this->messageData['receiver'] = $_POST['receiver'];
            $this->messageData['message'] = $_POST['message'];
            $this->model->sendMessage($this->messageData);
            header("Location: /messages");
            exit();
        }
    }
?>