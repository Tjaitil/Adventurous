<?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "users";
        $mysqli = new mysqli($servername, $username, $password, $dbname);
        if($mysqli->connect_error) {
          die("Connection failed" . $mysqli->connect_error);
          echo "Something went wrong";
        }
        else {
          return $mysqli;
          echo "Connection successfull";
        }    
?>