<?php
class getcity extends dbh {
        public $farmleveld;
        public $farmxpd;
        public $fieldsd;
        public $cropType;
        public $cropdQuant;
        public $cropCountdownd;
        public $workforced;
        public $availWorkforced;
        public $cropType2d;
        public $cropQuant2d;
        public $cropCountdown2d;
        public $cropWorkforced;
        public $butchWorkforced;
        public $crop;
        public $user;
      function getData () {
            $sql = "SELECT farmlevel, experience, fields, workforce, avail_workforce, crop_type, crop_quant, crop_countdown, crop_type2,
            crop_quant2, crop_countdown2, crop_workforce, butch_workforce FROM Farmer WHERE Username='tjaitil'";
            $result = $this->connect()->query($sql);
            if ($result->num_rows > 0 ) {
            while ($row = $result->fetch_assoc()) {
                 $this->farmleveld =  $row['farmlevel'];
                 $this->farmxpd = $row['experience'];
                 $this->fieldsd = $row['fields'];
                 $this->workforced = $row['workforce'];
                 $this->availWorkforced = $row['avail_workforce'];
                 $this->cropType = $row['crop_type'];
                 $this->cropdQuant = $row['crop_quant'];
                 $this->cropCountdownd = $row['crop_countdown'];
                 $this->cropType2d = $row['crop_type2'];
                 $this->cropQuant2d = $row ['crop_quant2'];
                 $this->cropCountdown2d = $row['crop_countdown2'];
                 $this->cropWorkforced = $row['crop_workforce'];
                 $this->butchWorkforced = $row['butch_workforce'];
            }
            }

      }
}
?>