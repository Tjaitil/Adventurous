<?php
    class newuser_model extends model {
        public $username;
        
        function __construct ($username) {
            parent::__construct();
            $this->username = $username;
        }
        
        public function selectProfiency($profiency) {
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$row['profiency'] == 'none') {
                return false; 
            }
            
            $profiencies = array('Farmer', 'Miner', 'Warrior');
            if (array_search($profiency, $profiencies) === false) {
                header("Location: /maintenance");
                exit();
                return false;
            }
            
            $data = array();
            $data['Farmer'] = array('farmer', 'towhar');
            $data['Miner'] = array('miner', 'golbak');
            $data['Warrior'] = array('warrior', 'tasnobil');
            
            $sql = "UPDATE user_data SET location=:location, destination=:destination, profiency=:profiency WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":destination", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_location = $data[$profiency][1];
            $param_profiency = $data[$profiency][0];
            $param_username = $this->username;
            $stmt->execute();
            
            $_SESSION['gamedata']['profiency'] = $param_profiency;
            $this->db->closeConn();
        }
    }
?>