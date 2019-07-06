<?php
    class setcrops extends controller  {
        public $error = array('cropErr' => '', 'QuantityErr' => '', 'workforceErr' => '');
        public $cropType;
        public $cropQuantity;
        public $workforceQuant;
        public $updateCropData = array();
        public $model;
        public $fields;
        
        function __construct() {
            parent::__construct();
        }
        
        public function checkData () {
            if (empty($_POST['cropType'])) {
                $this->error['cropErr'] = "Please select a crop type";
            }
            if ($_POST['cropQuantity'] === 0 || empty($_POST['cropQuantity'])) {
                $this->error['cropQuantityErr'] = "Please enter a valid number";
            }
            else if ($_POST['cropQuantity'] > $this->fieldsd) {
                $this->error['cropQuantityErr'] = "You don't have that many fields available";
            }
            if ($_POST['workforceQuant'] === 0 || empty($_POST['workforce'])) {
                $this->error['workforceErr'] = "Please select number of workers";
            }
            else if ($_POST['workforceQuant'] > $this->availWorkforced) {
                $this->error['workforceErr'] = "You don't have that many workforce";
            }
            if(!empty($this->error['crop']) || !empty($this->error['cropQuantityErr']) || empty($this->error['workforceErr'])) {
                return $this->error;
            }
            else {
                $cropType = trim($_POST['cropType']);
                $this->updateCropData['cropQuantity'] = $_POST['cropQuantity'];
                $this->updateCropData['workforce'] = $_POST['workforceQuant'];
                require('helpers/include_model.php');
                $this->model = include_model(true, 'setcrops', false);
                $getCropTypeData = $this->model->getData($cropType);
            }
        }
        
        public function prepareCropData ($row) {
            $cropTypeTime = $row['time'];
            $cropTypelevel = $row['level'];
            $cropTypeExperience = $row['experience'];
            $addTime = $cropTypeTime * $this->updateCropData['cropQuantity'] / $this->updateCropData['workforceQuant'];
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            $updateCropData['cropCountdown'] = date_format($newDate, "Y-m-d H:i:s");
            $updateCropData['fieldsAvailable'] = $this->field - $this->cropQuantity;
            $updateCropData['newWorkforce'] = $this->availWorkforced - $this->workforce;
            $updateCropData['experience'] = $this->cropQuantity * $this->cropXP + $this->farmxpd;
            $this->model->updateData($updateCropData);
        }
    }
?>