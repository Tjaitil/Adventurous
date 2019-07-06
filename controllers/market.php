<?php
    class market extends controller {
        public $data = array();
        public $post_data = array();
        public $error = array("itemErr" => '', "amountErr" => '', "priceErr" => '', "typeErr" => '');
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('market', true);
            $this->data = $this->model->getData();
            $this->post();
            $this->renderWE('market', 'Market', $this->data, $this->error);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $result = $formhandler->checkData($_POST);
                if(array_search("Empty!", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value; 
                        }
                        $_SESSION['gamedata']['game_message'] = "ERROR: There were one or more errors in your submission";
                    return false;
                }
                else {
                    $this->prepareData();
                }
            }
            else {
                return false;
            }
        }
        
        public function prepareData() {
            $this->post_data['item'] = strtolower($_POST['item']);
            $this->post_data['amount'] = $_POST['amount'];
            $this->post_data['price'] = $_POST['price'];
            $this->post_data['type'] = $_POST['type'];
            $this->model->newOffer($this->post_data);
        }
    }
?>