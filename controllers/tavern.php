<?php
    class tavern extends controller  {
        public $data = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('tavern', true);
            $this->data['user_tavern_data'] = $this->model->getData($_SESSION['gamedata']['location']);
            $this->workers();
            $this->data['persons'] = $this->model->getPersons($_SESSION['gamedata']['location']);
            $this->render('tavern', 'Tavern', $this->data);
        }
        
        public function workers() {
            var_dump($this->data['user_tavern_data']['new_workers']);
            $date = date("Y-m-d");
            var_dump($date);
            $city = $_SESSION['gamedata']['location'];
            if($date != $this->data['user_tavern_data']['new_workers'] && $this->data['user_tavern_data'][$city] == 0) {
                echo "workers";
                $city = $_SESSION['gamedata']['location'];
                switch($city) {
                    case 'snerpiir' || 'golbak':
                        $farmer_amount = rand(0,2);
                        $miner_amount = rand(0,3);
                        $warrior_amount = rand(1,2);
                        break;
                    case 'towhar' || 'krasnur':
                        $farmer_amount = rand(0,3);
                        $miner_amount  = rand(0,2);
                        $warrior_amount = rand(0,2);
                        break;
                    case 'tasnobil' || 'parth':
                        $farmer_amount = rand(0,2);
                        $miner_amount  = rand(0,2);
                        $warrior_amount = rand(0,5);
                        break;
                }
                $warrior_types = array('melee', 'archer');
                for($i = 0; $i < $warrior_amount; $i++) {
                    shuffle($warrior_types);
                    $this->data['warrior_amount'][$i]['type'] = $warrior_types[0];
                    $this->data['warrior_amount'][$i]['level'] = rand(1,3);
                }
                $this->model->updateWorkers($_SESSION['gamedata']['location'], $farmer_amount, $miner_amount, $this->data['warrior_amount']);
            }
            else if($date === $this->data['user_tavern_data']['new_workers']) {
                $this->data['tavern'] = $this->model->getWorkers($_SESSION['gamedata']['location']);
            }
        }
    }
?>