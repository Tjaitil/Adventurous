<?php
    class getWorkforce extends dbh {        
        public $cropWorkforce;
        public $butchWorkforce;
        public $avaWorkforce;
        public $workforce;
        public $user;
        function __construct ($session) {
            $this->user = $session;
            $sql = "SELECT Workforce, Avail_workforce, Crop_workforce, Butch_workforce FROM Farmer WHERE Username='$this->user'";
            $result = $this->connect()->query($sql);
            $numrows = $result->num_rows;
            if ($numrows > 0 ) {
                while ($row = $result->fetch_assoc()){
                    $this->workforce = $row['Workforce'];
                    $this->avaWorkforce = $row['Avail_workforce'];
                    $this->cropWorkforce = $row['Crop_workforce'];
                    $this->butchWorkforce = $row['Butch_workforce'];
                }
            }
        }
        function echoResults () {
            echo $this->workforce;
            echo $this->avaWorkforce;
        }
    }
?>