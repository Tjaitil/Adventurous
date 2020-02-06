<?php
    class stockpile extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Stockpile', true);
            $this->data = $this->model->getStockpile();
            $this->data['max_amount'] = 60;
            $this->render('stockpile', 'Stockpile', $this->data);
        }
    }
?>