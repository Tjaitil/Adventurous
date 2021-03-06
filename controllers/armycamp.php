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
            /*$this->data['warrior_level_up'] = $this->model->checkWarriorLevel($this->data['warrior_data']);*/
            $this->render('armycamp', 'Army Camp', $this->data, true, true);
        }
    }
?>