<?php
    class getButchData extends dbh {
        public;
        public;
        public $user;
        
        function __construct ($session) {
            $this->user = $session;
            $sql = "SELECT .... FROM Butch WHERE Username='$this->user'";
            $result = $this->connect()->query($sql);
            $numrows = $result->num_rows;
            if ($numrows > 0 ) {
                while ($row = $result->fetch_assoc()) {
                    
                }
            }
        }
    }


?>