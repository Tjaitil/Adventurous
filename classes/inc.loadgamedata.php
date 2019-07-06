<?php
    class loadGameData extends dbh{
        public $newUser;
        public $sesUser;
        public $location;
        public $mineLevel;
        public $mineXP;
        public $mineNLevel;
        public $farmLevel;
        public $farmXP;
        public $farmNLevel;
        public $warriorLevel;
        public $warriorXP;
        public $warriorNLevel;
        public $traderLevel;
        public $traderXP;
        public $traderNLevel;
        
        function __construct ($sesUser) {
            $this->user = $sesUser;
            $stmt->$this->connect()->prepare("SELECT username FROM user_info WHERE username=?"); 
            $stmt->bind_param('s', $this->user);
            $stmt->execute();
            $stmt->fetch();
            if ($stmt->num_rows == 1) {
                $stmt2 = $this->connect()->prepare("") // JOIN tables eller Transaction;
                $stmt2->bind_param("");
                $stmt->execute();
                $stmt->fetch();
                if ($stmt->num_rows > 0) {
                while ($row = $stmt->fetch_assoc()) {
                    $_SESSION['location'] = $this->location = $row['city'];
                }
                header('/main.php');    
            }
        }
    }

?>