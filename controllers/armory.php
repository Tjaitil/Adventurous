<?php
    class armory extends controller {
        public $data;
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Armory', true);
            $this->data['warrior_armory'] = $this->model->getData();
            $this->render('armory', 'Armory', $this->data);
        }
    }
?>