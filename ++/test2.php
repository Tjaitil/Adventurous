<?php
    class testing extends dbh  {
        public $mail;
        public $user = "tjaitil";
        
        function __construct () {
            $sql2 = "SELECT email FROM user_info WHERE username='$this->user'";
            $result2 = $this->connect()->query($sql2);
            if ($result2->num_rows > 0) {
                while ($row2 = $result2->fetch_assoc()) {
                    $this->mail = $row2['email'];
                }
                
            }
            else {
                echo "Something went wrong";
            }
            echo "Construct is finished";
        }
    }
?>