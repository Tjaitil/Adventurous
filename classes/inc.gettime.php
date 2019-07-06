<?php
    class getTime extends dbh {
        public $datetime;
        public $date;
        public $cropCountdownd;
        public $user;
        function __construct ($session) {
            $this->user = $session;
            $sql = "SELECT Crop_countdown FROM Farmer WHERE Username='$this->user'";
            $result = $this->connect()->query($sql);
            $numrows = $result->num_rows;
            if ($numrows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->cropCountdownd = $row['Crop_countdown'];
                }
                $this->datetime = new DateTime($this->cropCountdownd);
                $this->date = date_timestamp_get($this->datetime);
                echo $this->date; 
            }
        }
    }
?>