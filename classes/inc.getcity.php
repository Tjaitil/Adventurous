<?php
    class getCity {
        public $user;
        public $city;
        function __construct () {
            $sql = "SELECT City FROM Users WHERE Username='tjaitil'";
            $result = $mysqli->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $this->city = $row['City'];
                }
            }
        }
        function echoResult() {
            echo $this->city;
        }
    }
?>