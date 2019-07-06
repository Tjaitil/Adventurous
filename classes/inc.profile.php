<?php
    class profile {
        private $conn;
        function __construct ($conn){
            $this->conn = $conn;
        }
        function updatepassword () {
            try {
                $password = '123asd1234';
                $user = "tjaitil";
                $sql = "UPDATE users SET password= :password WHERE username= :username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam('password', $param_password, PDO::PARAM_STR);
                $param_username = $user;
                $param_password = password_hash($password, PASSWORD_DEFAULT);
                echo $param_password;
                $stmt->execute();
            }
            catch (PDOEXception $e){
                echo "Error:" . $e->getMessage();
            }
        }
        function getpassword () {
            try {
                $user = "tjaitil";
                $sql = "SELECT password FROM users WHERE username= :username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $user;
                $stmt->execute();
                $row = $stmt->fetch();
                $password = '123asd1234';
                /*$password = password_hash($password, PASSWORD_DEFAULT);*/
                $password2 = '123asd1234';
                $password2 = password_hash($password, PASSWORD_DEFAULT);
                $database = $row['password'];
                echo "</br>";
                /* echo "Password 1:" . "" . $password . "</br>";
                echo "Password 2:" . "" . $password2 . "</br>";*/
                echo $row['password'] . "</br>";
                
                if (password_verify($password, $database)) {
                    echo "Password verified";
                }
                else {
                    echo "Not matchin passwords";
                }
                /*8dcc19b8694cfbe10f15a3c8b3b8fc32e39d4366cc0c818d9390dfe5728dd6cf
                8dcc19b8694cfbe10f15a3c8b3b8fc32e39d4366cc0c818d9390dfe5728dd6cf*/
            }
            catch (PDOException $e) {
                echo "Error:" . $e->getMessage();
            }
        }
    }
?>