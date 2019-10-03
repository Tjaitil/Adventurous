<?php
    class farm_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function checkCountdown() {
            $sql = "SELECT grow_countdown FROM farmer WHERE username= :username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $cropCountdown = $row['crop_grow_countdown'];
            $datetime = new DateTime($cropCountdown);
            $date = date_timestamp_get($datetime);
            $this->db->closeConn();
            echo $date;
        }
        // Fetches crop and butchery data for farm
        public function fetchData () {
            $sql = "SELECT experience, fields, workforce, avail_workforce, grow_type, grow_quant,
            grow_countdown FROM Farmer WHERE Username= :username";
            $stmt = $this->db->conn->query($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->fieldsd = $row['fields'];
            return $row;
        }
    }
?>