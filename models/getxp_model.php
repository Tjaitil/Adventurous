<?php
    class getxp extends model {
        public $session;
        public $user;
        public $profiencyLevel;
        public $nextLevel;
        public $userXP;
        
        
        function __construct ($session) {
            parent::__construct();
            $this->session;
        }
        
        function fetchData($user) {
            $this->user = $user;
            $sql = "SELECT profiency_xp, profiency_xp_nextlevel FROM user_data WHERE username= :username";
            $stmt = $this->conn->query($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->user;
            if ($stmt->execute()) {
                while ($row = $stmt->fetchData(PDO::FETCH_ASSOC)) {
                    $this->userXP = $row['profiency_xp'];
                    $this->nextLevel = $row['profiency_xp_nextlevel'];
                }
            }
            else {
                echo "soemthing went wrong";
            }
            $this->closeConn();
        }
        
        function echoData() {
            echo $this->userXP;
            echo $this->nextLevel;
        }
    }

?>