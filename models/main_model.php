<?php
    class Main_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getData() {
            $data = array();
            $data['chat'] = $this->getChat();
            return $data;
        }
        
        public function getChat($clock = false) {
            $sql = "SELECT id, clock, username, message FROM public_chat ORDER BY clock DESC LIMIT 30";
            $stmt = $this->conn->query($sql);
            $stmt->execute();
            if($clock == false) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            else {
                $sql = "SELECT clock, username, message FROM public_chat
                        WHERE clock > (SELECT id FROM public_chat WHERE clock=:clock ORDER BY ID ASC LIMIT 1) AS time";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":clock", $param_clock, PDO::PARAM_STR);
                $param_clock = $clock;
                $stmt->execute();
                $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        
        public function chat($message) {
            $sql = "INSERT INTO public_chat (clock, username, message) VALUES (:clock, :username, :message)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":clock", $param_clock, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":message", $param_message, PDO::PARAM_STR);
            $param_clock = date("H:i:s");
            $param_username = $this->username;
            $param_message = $message;
            $stmt->execute();
            $data = $this->getChat($param_clock);
            get_template('chat', $data, true);
        }
    }
?>