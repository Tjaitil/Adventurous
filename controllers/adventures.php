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
            $this->post();
            $this->renderWE('adventures', 'Adventures', $this->data, $this->error, true);
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
            $this->loadModel('SetAdventure', true);
            $adventure_status = $this->model->checkAdventure()['adventure_id'];
            if($adventure_status != 0) {
                $this->gameMessage("ERROR: Finish your current adventure before starting a new one");
            }
            
            $difficulties = array("easy" => 1.0, "medium" => 5.0, "hard" => 12);
            if($difficulties[$_POST['difficulty']] > $_SESSION['gamedata']['adventurer_respect']) {
                $this->gameMessage("ERROR: Adventurer respect too low for this difficulty");    
            }
            $this->adventureData['difficulty'] = $_POST['difficulty'];
            $this->adventureData['location'] = $_POST['location'];
            $this->adventureData['other_invite'] = (isset($_POST['other_invite'])) ? 1 : 0;
            
            $this->model->newAdventure($this->adventureData);
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