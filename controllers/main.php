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
            $this->render('main', 'Main', false);
        }
        function fetchData() {
            $this->loadModel('gamedata', true);
            $this->gamedata = $this->model->fetchData();
            $this->loadModel('Main', true);
            $this->data = $this->model->getData();
            $_SESSION['gamedata'] = $this->gamedata;
            $_SESSION['gamedata']['travelling'] = false;
            $profiency = $_SESSION['gamedata']['profiency'];
            $_SESSION['gamedata']['profiency_level'] = $_SESSION['gamedata'][$profiency]['level'];
            $_SESSION['gamedata']['profiency_xp'] = $_SESSION['gamedata'][$profiency]['xp'];
            $_SESSION['gamedata']['profiency_xp_nextlevel'] = $_SESSION['gamedata'][$profiency]['next_level'];
            $_SESSION['gamedata']['game_message'] = 's';
            $_SESSION['gamedata']['conversation']['progress'] = '';
        }
    }
?>