<?php
    class armycamp extends controller {
        public $data;
        public $warrior_data = array();
        public $error = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('ArmyCamp', true);
            $this->data = $this->model->getData();
            for($i = 0; $i < count($this->data['warrior_data']); $i++) {
                if($this->data['warrior_data'][$i]['mission'] < 1) {
                    $this->data['warrior_data'][$i]['status'] = "On Mission: " . $this->data['warrior_data'][$i]['mission'];
                }
                else if($this->data['warrior_data'][$i]['mission'] == 1) {
                    $this->data['warrior_data'][$i]['status'] == "On Adventure";
                }
                if($this->data['warrior_data'][$i]['fetch_report'] == 1) {
                    $this->data['warrior_data'][$i]['status'] = "Training";
                }
                if($this->data['warrior_data'][$i]['rest'] == 1) {
                    $this->data['warrior_data'][$i]['status'] = "Resting";
                }
                else {
                    $this->data['warrior_data'][$i]['status'] = "Nothing special";
                }
            }
            $this->data['warrior_level_up'] = $this->model->checkWarriorLevel($this->data['warrior_data']);
            $this->profiencyCheck('warrior');
            $this->post();
            $this->renderWE('armycamp', 'Army Camp', $this->data, $data = 5);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $result = $formhandler->checkData($_POST);
                if(array_search("empty", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value; 
                        }
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
            $this->warrior_data['id'] = $_POST['id'];
            $this->warrior_data['type'] = $_POST['type'];
            $this->loadModel('settraining', true);
            $training_type_data = $this->model->getTrainingTypeData($this->warrior_data['type'], $this->warrior_data['id']);
            if($training_type_data != false) {
                $this->prepareData($training_type_data);    
            }
        }
        
        public function prepareData($training_type_data) {
            $training_type_time = $training_type_data['training_type']['time'];
            $addTime = $training_type_time;
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$addTime} seconds");
            $this->warrior_data['training_countdown'] = date_format($new_date, "Y-m-d H:i:s");
            $this->warrior_data['user_experience'] = $training_type_data['training_type']['experience'];
            $this->warrior_data['warrior_xp'] = $_SESSION['gamedata']['warrior']['xp'];
            $this->model->setTrainingData($this->warrior_data);
        }
    }
?>