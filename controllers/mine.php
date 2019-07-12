<?php
    class mine extends controller  {
        public $data = array();
        public $error = array("typeErr" => '', "workforceErr" => '', "profiencyErr" => '', "permitErr" => '', "workErr" => '');
        public $dbAvail_workforce;
        public $permits;
        public $mining_data = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('mine', true);
            $this->data = $this->model->getData();
            $this->dbAvail_workforce = $this->data['workforceData']['avail_workforce'];
            $this->permits = $this->data['minerData']['permits'];
            $this->post();
            $this->renderWE('mine', 'Mine', $this->data, $this->error);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                $db_test = $this->model->checkCountdown($check = true);
                if($db_test == false) {
                    $this->model->gameMessage("ERROR: You are already mining!");
                    return false;
                }
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $result = $formhandler->checkData($_POST);
                if(array_search("Empty!", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value;
                        }
                    $this->model->gameMessage("ERROR: There were one or more errors in your submission");
                    return false;
                }
                if($_POST['workforce'] > $this->dbAvail_workforce) {
                    $this->model->gameMessage("ERROR: You dont have that many workers available");
                    return false;
                }
                $type = $_POST['type'];
                $result_explode = explode('|', $type);
                if($result_explode[0] === '0') {
                    $this->model->gameMessage("ERROR: Select a mining type");
                    return false;
                }
                if($result_explode[1] > $this->permits) {
                    $this->model->gameMessage("ERROR: You don't have enough mining permits");
                    return false;
                }
                if($this->data['minerData']['fetch_minerals'] == "1") {
                    $this->model->gameMessage("ERROR: There is already work going on!");
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
        
        //Bind $_POST variables to variables
        public function bindData() {
            $result_explode = explode('|', $_POST['type']);
            $this->mining_data['mineral_type'] = $result_explode[0];
            $this->mining_data['workforce_quant'] = $_POST['workforce'];
            $this->loadModel('SetMine', true);
            $mineral_data = $this->model->getMineralTypeData($this->mining_data['mineral_type']);
            if($mineral_data != false) {
                $this->prepareData($mineral_data);
            }
        }
        
        public function prepareData($mineral_data) {
            $addTime = $mineral_data['time']; - (1.5 * $this->data['workforceData']['effect_level']);
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            $this->mining_data['mining_countdown'] = date_format($newDate, "Y-m-d H:i:s");
            $this->mining_data['permits'] = $this->data['minerData']['permits'] - $mineral_data['permit_cost'];
            $this->mining_data['new_workforce'] = $this->dbAvail_workforce - $this->mining_data['workforce_quant'];
            $this->mining_data['experience'] = $mineral_data['experience'] * 0.20;
            $this->model->setMineData($this->mining_data);
        }
    }
?>