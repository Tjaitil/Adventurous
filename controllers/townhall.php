<?php
    class townhall extends controller {
        public $data;
        
        function __construct() {
            parent::__construct();
        }
        public function index() {
            $this->checkLocation();
            $this->loadModel('townhall', true);
            $this->data = $this->model->getData();
            /*$this->data['favor'] = $this->favorGenerate();*/
            $this->determineAssignment();
            $this->render('townhall', 'Town Hall', $this->data);
        }
        private function checkLocation() {
            $location = str_replace(" ", "", $_SESSION['gamedata']['location']);
            $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
            if(array_search($location, $locations) === false) {
                header("Location: /city");
                die();
            }
        }
        private function determineAssignment() {
            if($this->data['trader_data']['assignment_id'] != 0) {
                $this->data['trader_data']['assignment'] =
                "Carrying " . $this->data['trader_data'][0]['cargo'] . " from " . $this->data['trader_data'][0]['base'] . " to " .
                $this->data['trader_data'][0]['destination'] .  ", " . "delivered " . $this->data['trader_data']['delivered'] . "/" .
                $this->data['trader_data'][0]['assignment_amount'] . " ({$this->data['trader_data'][0]['assignment_type']})";
            }
            else {
                $this->data['trader_data']['assignment'] = "none";
            }
        }
    }
?>