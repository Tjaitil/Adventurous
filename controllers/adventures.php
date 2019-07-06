<?php
    class adventures extends controller {
        public $error = array('difficultyErr' => '', 'locationErr' => '', 'adventureErr' => '');
        public $data;
        public $adventureData = array();
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('adventures', true);
            $this->data = $this->model->getData();
            if($this->data['current_adventure']['current'] != 0) {
                $this->calculateContribution();
            }
            $this->post();
            $this->renderWE('Adventures', 'Adventures', $this->data, $this->error);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $postArray = $_POST;
                $result = $formhandler->checkData($postArray);
                if(array_search("Empty!", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value; 
                        }
                }
                if(!$this->model->checkAdventure() > 0) {
                    return false;
                }
                else {
                    $this->bindData();
                }
            }
            else {
                return false;
            }
        }
        
        public function bindData() {
            $this->adventureData['difficulty'] = $_POST['difficulty'];
            $this->adventureData['location'] = $_POST['location'];
            $this->loadModel('setadventure', true);
            $this->model->newAdventure($this->adventureData);
            header("Location: /adventures");
            exit();
        }
        
        public function calculateContribution() {
            
            $profiencies = array('farmer', 'miner', 'warrior');
            
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
                $this->data['current_adventure'][$profiencies[$i]]['missing_contribution'] =
                 $this->data['current_adventure']['requirements'][$i]['amount'] -
                 intval($this->data['current_adventure'][$profiencies[$i]]['provided']);
            }
            var_dump($this->data['current_adventure']['requirements'][3]['amount']);
            var_dump($this->data['current_adventure']['trader_diplomacy']);
            if($this->data['current_adventure']['requirements'][3]['amount'] > $this->data['current_adventure']['trader_diplomacy']) {
                $this->data['current_adventure']['trader']['missing_contribution'] = "Not high enough diplomacy relation";
            }
            else {
                $this->data['current_adventure']['trader']['missing_contribution'] = "Diplomacy relations is high enough";
            }
        }
    }
?>