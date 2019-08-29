<?php
    class crops extends controller {
        public $data;
        private $POST_array;
        public $error = array("cropTypeErr" => '', "quantityErr" => '', "workforceErr" => '');
        public $cropQuantity;
        public $workforceQuant;
        public $cropData = array();
        public $model;
        public $dbFields;
        public $dbAvail_workforce;
        
        function __construct() {
          parent::__construct();
        }
        
        
        public function index() {
            $this->profiencyCheck('farmer');
            $this->loadModel('Crops', true);
            $this->data = $this->model->getData();
            $this->dbfields = $this->data['fields']['fields_avail'];
            $this->dbAvail_workforce = $this->data['workforce_data']['avail_workforce'];
            $this->post();
            $this->renderWE('crops', 'Crops', $this->data, $this->error);
        }
        
        public function post() {
            if($_SERVER['REQUEST_METHOD'] === "POST") {
                $db_test = $this->model->checkCountdown($check = true);
                if($db_test == false) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: There are already crops growing!";
                    return false;
                }
                $this->dbFields = $this->data['fields']['fields_avail'];
                $this->dbAvail_workforce = $this->data['workforce_data']['avail_workforce'];
                require_once(constant('ROUTE_HELPER') . 'formhandler.php');
                $formhandler = new formhandler();
                $postArray = $_POST;
                $result = $formhandler->checkData($postArray);
                if (array_search("Empty!", $result) !== false) {
                        foreach($result as $key => $value) {
                           $this->error[$key] = $value;
                        }
                    $_SESSION['gamedata']['log'][]  = "ERROR: There were one or more errors in your submission";
                    return false;
                }
                if($_POST['quantity'] > $this->dbFields) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: You don't have that many fields available";
                }
                else if($_POST['workforce'] > $this->dbAvail_workforce) {
                    $_SESSION['gamedata']['log'][]  = "ERROR: You dont have that many workers available";
                }
                else {
                    $this->bindData();
                }
            }
            else {
                return false;
            }
        }
        
        //Bind $_POST variables to variables
        public function bindData () {
                $this->cropData['type'] = trim($_POST['type']);
                $this->cropData['quantity'] = $_POST['quantity'];
                $this->cropData['workforce_quant'] = $_POST['workforce'];
                $this->loadModel('Setcrops', true);
                $data = $this->model->getCropTypeData($this->cropData['type']);
                if($data != false) {
                    $this->prepareCropData($data);
                }
        }
        
        // Prepare data for database update
        public function prepareCropData ($data) {
            $crop_type_time = $data['crop']['time'];
            $crop_type_level = $data['crop']['farmer_level'];
            $crop_effectivity = 5 * $this->data['workforce_data']['effect_level'];
            $addTime = $crop_type_time * $this->cropData['quantity'] / $this->cropData['workforce_quant'] - $crop_effectivity;
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            $this->cropData['countdown'] = date_format($newDate, "Y-m-d H:i:s");
            $this->cropData['fields_available'] = $this->dbFields - $this->cropData['quantity'];
            $this->cropData['new_workforce'] = $this->dbAvail_workforce - $this->cropData['workforce_quant'];
            // Hente experience fra getCropTypeData
            $this->cropData['experience'] = 30;
            $this->cropData['seed_required'] = $data['crop']['seed_required'];
            $this->model->setCropData($this->cropData);
        }
    }
?>