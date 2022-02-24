<?php
    class item_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getPrices() {
            $param_username = $this->username;
            $sql = "SELECT a.store_value, b.item 
                    FROM items as a INNER JOIN inventory as b ON a.name = b.item
                    WHERE b.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $sql = "SELECT a.store_value, b.item
                    FROM items as a INNER JOIN stockpile as b ON a.name = b.item
                    WHERE b.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->response->addTo("data", array_merge($row, $row2), array("index" => "prices"));
        }
    }
?>