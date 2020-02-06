<?php
    class TownHall_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getData() {
            $data = array();
            $sql2 = "SELECT assignment_id, cart, cart_amount, delivered,
                    (SELECT capasity FROM travelbureau_carts WHERE wheel= cart) as capasity FROM trader
                     WHERE username=:username";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $data['trader_data'] = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if($data['trader_data']['assignment_id'] != 0 ) {
                $sql4 = "SELECT base, destination, cargo, assignment_amount, assignment_type
                         FROM trader_assignments
                         WHERE assignment_id=:assignment_id";
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $data['trader_data']['assignment_id'];
                $stmt4->execute();
                $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
                array_push($data['trader_data'], $row4);
            }
            
            $sql = "SELECT assignment_id, base, destination, cargo, assignment_amount, time, assignment_type FROM trader_assignments
                     WHERE base=:base AND assignment_type='favor'";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":base", $param_city, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $stmt->execute();
            $data['favor_assignments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            
            $location = str_replace(" ", "", $this->session['location']);
            $sql = "SELECT {$location} FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $data['diplomacy'] = $stmt->fetch(PDO::FETCH_NUM);
            $this->db->closeConn();
            return $data;
        }
    }
?>