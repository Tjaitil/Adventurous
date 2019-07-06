<?php
class dbh {
     function connect() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "Adventurous";
        $mysqli = new mysqli($servername, $username, $password, $dbname);
        if($mysqli === false) {
          die("Connection failed" . $mysqli->connect_error);
          echo "Something went wrong";
        }
        else {
          return $mysqli;
          echo "Connection successfull";
        }
     }
     
}           
?>