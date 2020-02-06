<?php
    class tavern extends controller  {
        public $data = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Tavern', true);
            $this->data['user_tavern_data'] = $this->model->getData();
            $this->workers();
            $this->data['persons'] = $this->model->getPersons($_SESSION['gamedata']['location']);
            $this->render('tavern', 'Tavern', $this->data);
        }
        
        public function workers() {
            $date = date("Y-m-d");
            $city = $_SESSION['gamedata']['location'];
            if(intval($this->data['user_tavern_data'][$city]) === 0) {
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
                $warrior_types = array('melee', 'ranged');
                for($i = 0; $i < ($farmer_amount + $miner_amount + $warrior_amount); $i++) {
                    if($i < $farmer_amount) {
                        $this->data['workers'][$i]['type'] = 'farmer';
                        $this->data['workers'][$i]['level'] = 0;
                    }
                    else if ($i < ($farmer_amount + $miner_amount)) {
                        $this->data['workers'][$i]['type'] = 'miner';
                        $this->data['workers'][$i]['level'] = 0;
                    }
                    else {
                        $rand = array_rand($warrior_types);
                        $this->data['workers'][$i]['type'] = $warrior_types[$rand];
                        $this->data['workers'][$i]['level'] = rand(1,3);
                    }
                }
                $this->data['tavern']['workers'] = $this->data['workers'];
                $this->model->updateWorkers($this->data['workers']);
            }
            else if($date === $this->data['user_tavern_data']['new_workers']) {
                $this->data['tavern']['workers'] = $this->model->getWorkers();
            }
        }
    }
?>