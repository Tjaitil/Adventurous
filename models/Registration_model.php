<?php
    class Registration_model extends model {
        
        function __construct () {
            parent::__construct();
        }
        
        public function check($POST_Array) {
            require(constant('ROUTE_HELPER') . 'registration_check.php');
            return registration_check($this->db->conn, $POST_Array);
        }
        
        public function insertDb($POST_array) {
        /*if (empty($this->userErr) && empty($this->emailErr) && empty($this->passErr) && empty($this->passconfErr) && empty($this->countryOOErr)
                && empty($this->DOBErr) /*$this->DOBvalid === true*/
            
            $data = array();
            $data[0] = array($POST_array['username'], 1);
            $data[1] = array($POST_array['username'], 2);
            
            try{
                $this->db->conn->beginTransaction();
                
                $sql = "INSERT INTO adventure (username) VALUES(:username)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $POST_array['username'];
                $stmt->execute();
                
                $sql2 = "INSERT INTO diplomacy (username) VALUES(:username)";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt2->execute();
                
                $sql3 = "INSERT INTO farmer (username) VALUES(:username)";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt3->execute();
                
                $sql4 = "INSERT INTO farmer_workforce (username) VALUES(:username)";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt4->execute();
                
                $sql5 = "INSERT INTO messages (title, sender, receiver, message) VALUES(:title, :sender, :receiver, :message)";
                $stmt5 = $this->db->conn->prepare($sql5);
                $stmt5->bindParam(":title", $param_title, PDO::PARAM_STR);
                $stmt5->bindParam(":sender", $param_sender, PDO::PARAM_STR);
                $stmt5->bindParam(":receiver", $param_receiver, PDO::PARAM_STR);
                $stmt5->bindParam(":message", $param_message, PDO::PARAM_STR);
                $param_title = "Welcome!";
                $param_sender = "Adventurous team";
                $param_receiver = $param_username;
                $param_message = "WELCOME TO Adventurous!" .
                "We hope you will enjoy this game!" .
                "Regards" . 
                "Adventurous Team";
                $stmt5->execute();
                
                $sql6 = "INSERT INTO miner (username) VALUES(:username)";
                $stmt6 = $this->db->conn->prepare($sql6);
                $stmt6->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt6->execute();
                
                $sql7 = "INSERT INTO miner_workforce (username) VALUES(:username)";
                $stmt7 = $this->db->conn->prepare($sql7);
                $stmt7->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt7->execute();
                
                $sql8 = "INSERT INTO stockpile (username, item, amount) VALUES(:username, 'Gold', 500)";
                $stmt8 = $this->db->conn->prepare($sql8);
                $stmt8->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt8->execute();
                
                $sql9 = "INSERT INTO tavern_times (username) VALUES(:username)";
                $stmt9 = $this->db->conn->prepare($sql9);
                $stmt9->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt9->execute();
                
                $sql10 = "INSERT INTO trader (username) VALUES(:username)";
                $stmt10 = $this->db->conn->prepare($sql10);
                $stmt10->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt10->execute();
                
                $sql11 = "INSERT INTO users (username, password, email, country, dateofbirth) VALUES(:username, :password,
                :email, :country, :dateofbirth)";
                $stmt11 = $this->db->conn->prepare($sql11);
                $stmt11->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt11->bindParam(':password', $param_password, PDO::PARAM_STR);
                $stmt11->bindParam(":email", $param_email, PDO::PARAM_STR);
                $stmt11->bindParam(":country", $param_country, PDO::PARAM_STR);
                $stmt11->bindParam(":dateofbirth", $param_dateofbirth, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $param_password = password_hash($POST_array['password'], PASSWORD_DEFAULT);
                $param_email = $POST_array['email'];
                $param_country = $POST_array['country'];
                $param_dateofbirth = $POST_array['dob'];
                $stmt11->execute();
                                 
                $sql12 = "INSERT INTO user_data (username) VALUES(:username)";
                $stmt12 = $this->db->conn->prepare($sql12);
                $stmt12->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt12->execute();
                
                $sql13= "INSERT INTO user_levels (username) VALUES(:username)";
                $stmt13 = $this->db->conn->prepare($sql13);
                $stmt13->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt13->execute();
                
                $sql14 = "INSERT INTO warrior (username) VALUES(:username)";
                $stmt14 = $this->db->conn->prepare($sql14);
                $stmt14->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt14->execute();
                
                $sql15 = "INSERT INTO warriors (username, warrior_id) VALUES(?, ?)";
                $stmt15 = $this->db->conn->prepare($sql15);
                foreach ($data as $row){
                    $stmt15->execute($row);
                }
                
                /*$sql16 = "INSERT INTO warriors (username, warrior_id) VALUES(:username, 2)";
                $stmt16 = $this->db->conn->prepare($sql16);
                $stmt16->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt->execute();*/
                
                $sql17 = "INSERT INTO warrior_armory (username, warrior_id) VALUES(?, ?)";
                $stmt17 = $this->db->conn->prepare($sql17);
                foreach ($data as $row){
                    $stmt17->execute($row);
                }
                
                /*$sql18 = "INSERT INTO warrior_armory (username, warrior_id) VALUES(:username, 2)";
                $stmt18 = $this->db->conn->prepare($sql18);
                $stmt18->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt18->execute();*/
                
                $sql19 = "INSERT INTO warrior_levels (username, warrior_id) VALUES(?, ?)";
                $stmt19 = $this->db->conn->prepare($sql19);
                foreach ($data as $row){
                    $stmt19->execute($row);
                }
                
                /*
                $sql20 = "INSERT INTO warrior_levels (username, warrior_id) VALUES(:username, 2)";
                $stmt20 = $this->db->conn->prepare($sql20);
                $stmt20->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt20->execute();*/
                
                $sql21 = "INSERT INTO workforcelodge (username) VALUES(:username)";
                $stmt21 = $this->db->conn->prepare($sql21);
                $stmt21->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already defined in $stmt1;
                $stmt21->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            return true;
        }
    }
?>