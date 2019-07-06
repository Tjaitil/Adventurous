<?php
    class login {
        public $userErr, $passErr;
        public $username;
        public $password;
        public $hashedPassword;
        private $conn;
        function __construct ($conn) {
            $this->conn = $conn;
        }
        function loginUser() {
            if(empty($_POST["username"])) {
                $this->userErr = "Please enter username!";
            }
            else {
                $this->username = trim($_POST['username']);
                var_dump($this->username);
                echo "</br>";
            }
            // Check if password is empty
            if(empty(trim($_POST['password']))){
                $this->passErr = 'Please enter your password.';
            }
            else {
                $this->password = $_POST['password'];
                var_dump($this->password);
                echo "</br>";
             }
             // Validate credentials
             if(empty($this->userErr) && empty($this->passErr)){
             // Prepare a select statement
                $sql = "SELECT username, password FROM users WHERE username= :username";
                if($stmt = $this->conn->prepare($sql)){
                   // Bind variables to the prepared statement as parameters
                   $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                   $param_username = $this->username;
                   var_dump($this->username);
                   //LAGE EN SYSTEM SOM SJEKKER OM BRUKERNAVNET EKSISTERER!
                   echo "</br>";
                   if($stmt->execute()){
                        $row=$stmt->fetch(PDO::FETCH_ASSOC);
                            $this->username = $row['username'];
                            $this->hashedPassword = $row['password'];
                            var_dump($this->username);
                            var_dump($this->password);
                            echo "</br>";
                            var_dump($this->hashedPassword);
                            if (password_verify($this->password, $this->hashedPassword)) {
                                session_start();
                                $_SESSION['username'] = $this->username;
                                $_SESSION['loggedin'] = true;
                                header("location: /main.php");
                            }
                            else {   
                                // Display an error message if password is not valid
                                $this->passErr = "The password you entered was not valid";
                            }
                    }
                }
                else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
               // Close statement
                unset($stmt); 
            }
        // Close connection
        unset($this->conn);
        }
    }
?>