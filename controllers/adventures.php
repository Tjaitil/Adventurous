<?php
    class adventures extends controller {
        public $error = array('difficultyErr' => '', 'locationErr' => '', 'adventureErr' => '');
        public $data;
        public $adventureData = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('Adventures', true);
            $this->data = $this->model->getData();
            /*if($this->data['current_adventure']['current'] != 0) {
                $this->calculateContribution();
            }*/
            $this->render('adventures', 'Adventures', $this->data, $this->error, true, true);
        }
        public function calculateContribution() {
            $this->data['current_adventure']['requirments']['farmer'] = array();
            $this->data['current_adventure']['requirments']['miner'] = array();
            $this->data['current_adventure']['requirments']['trader'] = array();
            $this->data['current_adventure']['requirments']['warrior'] = array();
            
            foreach($this->data['current_adventure']['requirments'] as $key) {
                switch($key['role']) {
                    case 'farmer':
                        $this->data['current_adventure']['requirments']['farmer'][] = $key;
                        break;
                    case 'miner':
                        $this->data['current_adventure']['requirments']['miner'][] = $key;
                        break;
                    case 'trader':
                        $this->data['current_adventure']['requirments']['trader'][] = $key;
                        break;
                    case 'warrior':
                        $this->data['current_adventure']['requirments']['warrior'][] = $key;
                        break;
                }
            }
            
            /*$profiencies = array('farmer', 'miner', 'warrior');
            
            $keys = array("status");
            for($x = 0; $x < count($profiencies); $x++) {
                
                if($this->data['current_adventure'][$profiencies[$x]]['status'] == null) {
                    $this->data['current_adventure'][$profiencies[$x]]['status'] = 0;
                    $this->data['current_adventure'][$profiencies[$x]]['provided'] = 0;
                }
            }
            
            if($this->data['current_adventure']['current'] == 0) {
                return false;
            }
            for($i = 0; $i < count($profiencies); $i++) {
                if( $this->data['current_adventure'][$profiencies[$i]]) {
                    
                }
                else {
                $this->data['current_adventure'][$profiencies[$i]]['missing_contribution'] =
                 $this->data['current_adventure']['requirements'][$i]['amount'] -
                 intval($this->data['current_adventure'][$profiencies[$i]]['provided']);
                }
            }
            var_dump($this->data['current_adventure']['requirements'][3]['amount']);
            var_dump($this->data['current_adventure']['trader_diplomacy']);
            if($this->data['current_adventure']['requirements'][3]['amount'] > $this->data['current_adventure']['trader_diplomacy']) {
                $this->data['current_adventure']['trader']['missing_contribution'] = "Not high enough diplomacy relation";
            }
            else {
                $this->data['current_adventure']['trader']['missing_contribution'] = "Diplomacy relations is high enough";
            }*/
        }
    }
?>