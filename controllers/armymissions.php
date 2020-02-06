<?php
    class armymissions extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('ArmyMissions', true);
            $this->data = $this->model->getData();
            $this->render('armymissions', 'Army-Missions', $this->data);
        }
    }
?>