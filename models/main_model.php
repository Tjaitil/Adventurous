<?php
    class Main_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function getData() {
            $data = array();
            $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['diplomacy'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $data['chat'] = $this->getChat();
            
            $sql = "SELECT grow_countdown, location, plot1_harvest FROM farmer WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['farmer_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT mining_countdown, location, fetch_minerals FROM miner WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['miner_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT assignment_id FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['trader_countdown'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['army_mission'] = $stmt->fetch(PDO::FETCH_OBJ)->mission;
            
            $sql = "SELECT training_countdown, fetch_report FROM warriors WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['warriors_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
            return $data;
        }
        public function getChat($clock = false) {
            $sql = "SELECT id, clock, username, message FROM public_chat ORDER BY time ASC LIMIT 30";
            $stmt = $this->db->conn->query($sql);
            $stmt->execute();
            if($clock == false) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                $sql = "SELECT clock, username, message FROM public_chat
                        WHERE id >= (SELECT id FROM public_chat WHERE clock=:clock ORDER BY ID ASC LIMIT 1)
                        ORDER BY clock DESC";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":clock", $param_clock, PDO::PARAM_STR);
                $param_clock = $clock;
                $stmt->execute();
                $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                get_template('chat', $data, true);
            }
        }
        public function chat($message) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to enter new message into public chat
            $sql = "INSERT INTO public_chat (clock, username, message) VALUES (:clock, :username, :message)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":clock", $param_clock, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
            $param_clock = date("H:i:s");
            $param_username = $this->username;
            $param_message = $POST['message'];
            $stmt->execute();
            $this->getChat($param_clock);
        }
    }
?>