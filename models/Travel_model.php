<?php
    class Travel_model extends model {
        public $username;
        public $session;
        public $base;
        public $speed_reduction = 1;
        public $destination;
        public $base_data;
        public $destination_data;
        public $time;
        public $arrive_time;
        public $travelCountdown;

        //AJAX functions:
        //checkCountdown, updateLocation
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function checkCountdown() {
                $time = date("Y-m-d H:i:s");
                if(isset($_SESSION['gamedata']['arrive_time']) && $_SESSION['gamedata']['arrive_time'] > $time) {
                    $time = $_SESSION['gamedata']['arrive_time'];
                    $datetime = date_timestamp_get(new DateTime($time));
                    echo $datetime, '|', $_SESSION['gamedata']['destination'];
                    return;
                }
                $sql = "SELECT location, arrive_time, destination FROM user_data WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['gamedata']['arrive_time'] = $row['arrive_time'];
                $date = date_timestamp_get(new DateTime($row['arrive_time']));
                js_echo(array($date, $row['destination']));
                $this->db->closeConn();
        }
        public function getData($base, $destination, $travelCountdown) {
            $this->base = $base;
            $this->travelCountdown = $travelCountdown;
            $this->destination = $destination;
            $time = date("Y-m-d H:i:s");
            if ($this->base === $this->destination) {
                $this->gameMessage("You are already there!", true);
                return false;
            }
            if ($this->travelCountdown > $time) {
                $this->gameMessage("Still travelling", true);
                return false;                
            }
            else if ($this->travelCountdown < $time) {
                $sql = "SELECT horse FROM user_data WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row['horse'] !== 'none') {
                    $sql = "SELECT speed FROM travelbureau_horses WHERE type=:type";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
                    $param_type = $row['horse'];
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $this->speed_reduction = $row['speed'];
                }
                
                $queryArray = array();
                $queryArray[0] = $this->base;
                $queryArray[1] = $this->destination;
                $in  = str_repeat('?,', count($queryArray) - 1) . '?';
                $sql = "SELECT city, location_x, location_y FROM travel_times WHERE city IN ($in)";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->execute($queryArray);
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $this->baseData = $row[0];
                $this->destinationData = $row[1];
                $_SESSION['gamedata']['destination'] = $this->destination;
                            
                $this->calculateDistance($this->baseData['location_x'], $this->base_data['location_y'], $this->destination_data['location_x'],
                                            $this->destinationData['location_y']);
                $this->travel();
            }
            $this->db->closeConn();
        }
        public function calculateDistance($x1, $y1, $x2, $y2) {
            $tan1 = ($x1 - $x2);
            $tan2 = ($y1 - $y2);
            $distance = hypot($tan1, $tan2);
            $speed = 10 / $this->speed_reduction;
            $this->time = $distance / $speed * 50;
       }
        public function travel() {
            /*$this->time = intval($this->time);
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$this->time}seconds");
            $this->arrive_time = date_format($new_date, "Y-m-d H:i:s");*/
            $sql = "UPDATE user_data SET location=:location, destination=:destination, arrive_time=:arrive_time WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(':location', $param_location, PDO::PARAM_STR);
            $stmt->bindParam(':destination', $param_destination, PDO::PARAM_STR);
            $stmt->bindParam(':arrive_time', $param_arrive_time, PDO::PARAM_STR);
            $stmt->bindParam(':username', $param_username, PDO::PARAM_STR);
            $param_location = "travelling";
            $param_destination = $this->destination;
            $param_arrive_time = $this->arrive_time;
            $param_username = $this->username;
            $stmt->execute();
            $_SESSION['gamedata']['location'] = $param_location;
            $this->db->closeConn();
        }
        public function updateLocation($base, $destination, $db_countdown) {
            if($base === $destination) {
                return false;
            }
            $sql = "UPDATE user_data SET location=:destination WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":destination", $param_destination, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_destination = $destination;
            $param_username = $this->username;
            $stmt->execute();
            $_SESSION['gamedata']['location'] = $destination;
            $this->db->closeConn();
       }
    }
?>