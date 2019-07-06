<?php
    class travelbureau extends controller  {
        public $data;
        function __construct() {
            parent::__construct();
        }
        
        public function index () {
            $this->loadModel('travelbureau', true);
            $this->data = $this->model->getData($_SESSION['gamedata']['location']);
            $this->render('travelbureau', 'Travel Bureau', $this->data);
        }
    }
?>