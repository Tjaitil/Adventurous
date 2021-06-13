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
            $this->render('tavern', 'Tavern', $this->data, true, true);
        }
        
        public function workers() {
            $date = date("Y-m-d");
            $city = $_SESSION['gamedata']['location'];
            if(empty($this->data['user_tavern_data']['workers'])) {
                $this->data['tavern']['workers'] = array();   
                return false;
            }
            else if($date > $this->data['user_tavern_data']['workers']['new_workers']) {
                $this->model->resetData();
            }
            if(intval($this->data['user_tavern_data']['workers'][$city]) === 0 || 
                $date > $this->data['user_tavern_data']['workers']['new_workers']) {
                switch($city) {
                    case 'snerpiir' || 'golbak':
                        $farmer_amount = rand(0,2);
                        $miner_amount = rand(1,3);
                        $warrior_amount = rand(1,2);
                        break;
                    case 'towhar' || 'krasnur':
                        $farmer_amount = rand(1,3);
                        $miner_amount  = rand(0,2);
                        $warrior_amount = rand(0,2);
                        break;
                    case 'tasnobil' || 'cruendo':
                        $farmer_amount = rand(0,2);
                        $miner_amount  = rand(0,2);
                        $warrior_amount = rand(1,5);
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
            else if($date == $this->data['user_tavern_data']['workers']['new_workers']) {
                $this->data['tavern']['workers'] = $this->model->getWorkers();
            }
            
        }
    }
?>