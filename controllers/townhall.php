<?php
    class townhall extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('townhall', true);
            $this->data = $this->model->getData();
            $this->data['favor'] = $this->favorGenerate();
            $this->render('townhall', 'Town Hall', $this->data);
        }
        
        public function favorGenerate() {
            $location = $_SESSION['gamedata']['location'];
            $location_goods = array();
            $location_goods['hirtam'] = array('Tomato Seeds', 'Iron', 'Clay');
            $location_goods['pvitul'] = array('Tomato Seeds', 'Iron', 'Clay');
            $location_goods['khanz'] = array('Tomato Seeds', 'Iron', 'Clay');
            $location_goods['ter'] = array('Tomato Seeds', 'Iron', 'Clay');
            $location_goods['fansal plains'] = array('Tomato Seeds', 'Iron', 'Clay');
            
            
            $amount = rand(10,40);
            /*$amount['1'];
            $amount['2'];
            $amount['3'];
            $amount['4'];
            $amount['5'];
            $amount['6'];*/
            
            var_dump($location_goods[$location]);
            $random_key = array_rand($location_goods[$location], 1);
            $data = array();
            $data['item'] = $location_goods[$location][$random_key];
            $data['amount'] = $amount;
            $data['destination'] = 'golbak';
            $data['base'] = $_SESSION['gamedata']['location'];
            $_SESSION['gamedata']['favor'] = $data;
            var_dump($_SESSION['gamedata']['favor']);
        }
    }
?>