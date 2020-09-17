<?php 
    class merchant extends controller  {
        public $data;
        
        function __construct() {
            parent::__construct();    
        }
        
        public function index() {
            $this->loadModel('Merchant', true);
            $this->data = $this->model->getData();
            $this->determineAssignment();
            $this->render('merchant', 'Merchant', $this->data, true);
        }
        private function determineAssignment() {
            // Check if there a trader assignment and if it is then format the string
            if($this->data['trader_data']['assignment_id'] != 0) {
                $format = "Carrying %s from %s to %s, delivered %d/%d (%s)";
                
                $this->data['trader_data']['assignment'] = sprintf($format, ucwords($this->data['trader_data'][0]['cargo']),
                                                                            ucwords($this->data['trader_data'][0]['base']),
                                                                            ucwords($this->data['trader_data'][0]['destination']),
                                                                            $this->data['trader_data']['delivered'],
                                                                            $this->data['trader_data'][0]['assignment_amount'],
                                                                            ucwords($this->data['trader_data'][0]['assignment_type']));
            }
            else {
                $this->data['trader_data']['assignment'] = "none";
            }
        }
    }
?>