<?php
    class main extends controller {
        public $gamedata;
        public $data;

        function __construct() {
            parent::__construct();
        }
        function index() {
            if(!isset($_SESSION['gamedata'])) {
                $this->fetchData();
            }
            $this->loadModel('Main', true);
            $this->data = $this->model->getData();
            /*$this->calculateCountdowns();*/
            $this->render('main', 'Main', false);
        }
        function fetchData() {
            $this->loadModel('gamedata', true);
            $_SESSION['gamedata'] = $this->model->fetchData();
            $_SESSION['gamedata']['travelling'] = false;
            $profiency = $_SESSION['gamedata']['profiency'];
            $_SESSION['gamedata']['profiency_level'] = $_SESSION['gamedata'][$profiency]['level'];
            $_SESSION['gamedata']['profiency_xp'] = $_SESSION['gamedata'][$profiency]['xp'];
            $_SESSION['gamedata']['profiency_xp_nextlevel'] = $_SESSION['gamedata'][$profiency]['next_level'];
            $_SESSION['gamedata']['log'] = array();
            $_SESSION['gamedata']['log'][] = "Welcome to Adventurous!";
            $_SESSION['gamedata']['conversation']['progress'] = '';
            $this->model->checkMarket();
        }
        function calculateCountdowns() {
            $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
            foreach($this->data['farmer_countdowns'] as $key) {
            }
        }
    }
?>