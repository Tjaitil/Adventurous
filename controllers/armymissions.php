<?php
    class armymissions extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        
        public function index() {
            $this->loadModel('armyMissions', true);
            $this->data = $this->model->getData();
            $arrayCount = count($this->data['armyMissions']);
            for($i = 0; $i < $arrayCount; $i++) {
                $newString = str_replace(":", "|", $this->data['armyMissions'][$i]['reward']);
                $rewards = explode("|", $newString);
                $this->data['armyMissions'][$i]['reward'] = $rewards;
            }
            $this->render('armymissions', 'Army-Missions', $this->data);
        }
    }
?>