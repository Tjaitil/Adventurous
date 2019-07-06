<?php
    class merchant extends controller  {
        public $data;
        
        function __construct() {
            parent::__construct();    
        }
        
        public function index() {
            $this->loadModel('Merchant', true);
            $this->data = $this->model->getData();
            require(constant('ROUTE_HELPER') . 'splittext.php');
            $i = 0;
            foreach($this->data['trader_assignments'] as $key) {
                /*$this->data['trader_assignments'][$i]['cargo'] = str_replace("_", " ", $this->data['trader_assignments'][$i]['cargo']);*/
                $this->data['trader_assignments'][$i]['reward'] = splitText($key['reward']);
                $i++;
            }
            if($this->data['trader_data']['assignment_id'] != 0) {
                $this->data['trader_data']['assignment'] =
                "Carrying " . $this->data['trader_data'][0]['cargo'] . " from " . $this->data['trader_data'][0]['base'] . " to " .
                $this->data['trader_data'][0]['destination'] .  ", " . "delivered " . $this->data['trader_data']['delivered'] . "/" .
                $this->data['trader_data'][0]['cargo_amount'];
            }
            else {
                $this->data['trader_data']['assignment'] = "none";
            }
            $this->render('merchant', 'Merchant', $this->data);
        }
        
    }
?>