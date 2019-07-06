<?php
    class workforcelodge extends controller  {
        public $data = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('workforcelodge', true);
            $this->data = $this->model->getData($_SESSION['gamedata']['profiency']);
            $this->render('workforcelodge', 'Wokforce Lodge', $this->data);
        }
    }
?>