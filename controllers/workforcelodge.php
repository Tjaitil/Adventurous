<?php
    class workforcelodge extends controller  {
        public $data = array();
        
        function __construct() {
            parent::__construct();
        }
        public function index() {
            $this->loadModel('Workforcelodge', true);
            $this->data = $this->model->getData();
            $this->render('workforcelodge', 'Wokforce Lodge', $this->data, true);
        }
    }
?>