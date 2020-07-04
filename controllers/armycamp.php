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
            $this->data['warrior_level_up'] = $this->model->checkWarriorLevel($this->data['warrior_data']);
            for($i = 0; $i < count($this->data['warrior_data']); $i++) {
                if($this->data['warrior_data'][$i]['mission'] !== '0') {
                    $this->data['warrior_data'][$i]['status'] = "On Mission " . $this->data['warrior_data'][$i]['mission'];
                    continue;
                }
                if($this->data['warrior_data'][$i]['mission'] == 1) {
                    $this->data['warrior_data'][$i]['status'] = "On Adventure";
                    continue;
                }
                if($this->data['warrior_data'][$i]['fetch_report'] == 1) {
                    $this->data['warrior_data'][$i]['status'] = "Training";
                    continue;
                }
                if($this->data['warrior_data'][$i]['rest'] == 1) {
                    $this->data['warrior_data'][$i]['status'] = "Resting";
                    continue;
                }
                else {
                    $this->data['warrior_data'][$i]['status'] = "Nothing special";
                }
            }
            $this->render('armycamp', 'Army Camp', $this->data, true);
        }
    }
?>