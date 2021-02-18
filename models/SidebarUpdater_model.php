<?php
    class SidebarUpdater_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getDiplomacy($js = false) {
            $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if($js === false) {
                $this->data['diplomacy'] = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                echo json_encode($stmt->fetch(PDO::FETCH_NUM));
            }
        }
        public function getCountdownData($js = false) {
            $sql = "SELECT crop_countdown, location, plot1_harvest FROM farmer WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->data['farmer_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT mining_countdown, location, fetch_minerals FROM miner WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->data['miner_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT assignment_id FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->data['trader_countdown'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT mission, mission_countdown FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->data['army_mission'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT training_countdown, fetch_report, mission FROM warriors WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->data['warriors_countdowns'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function calculateCountdowns($js = true) {
            $this->getCountdownData(true);
            if($js == false) {
                $this->getDiplomacy();
            }
            $date_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));
            foreach($this->data['farmer_countdowns'] as $key) {
                $countdown = date_timestamp_get(new DateTime($key['crop_countdown']));
                if($countdown < $date_now && $key['plot1_harvest'] == 1) {
                    $this->data['countdowns']['farmer'][] = "finished";
                }
                else if($countdown > $date_now && $key['plot1_harvest'] == 1) {
                    $time_left = $countdown - $date_now;
                    $m = round($time_left / 60);
                    $this->data['countdowns']['farmer'][] = "done in " . $m . 'm';
                }
                else {
                    $this->data['countdowns']['farmer'][] = "nothing happening";
                }
            }
            foreach($this->data['miner_countdowns'] as $key) {
                $countdown = date_timestamp_get(new DateTime($key['mining_countdown']));
                if($countdown < $date_now && $key['fetch_minerals'] == 1) {
                    $this->data['countdowns']['miner'][] = "Finished";
                }
                else if($countdown > $date_now && $key['fetch_minerals'] == 1) {
                    $time_left = $countdown - $date_now;
                    $m = round($time_left / 60);
                    $this->data['countdowns']['miner'][] = "done in " . $m . 'm';
                }
                else {
                    $this->data['countdowns']['miner'][] = "nothing happening";
                }
            }
            $this->data['countdowns']['warrior']['training'] = 0;
            $this->data['countdowns']['warrior']['finished'] = 0;
            $this->data['countdowns']['warrior']['mission'] = 0;
            $this->data['countdowns']['warrior']['idle'] = 0;
            foreach($this->data['warriors_countdowns'] as $key) {
                $countdown = date_timestamp_get(new DateTime($key['training_countdown']));
                if($countdown < $date_now && $key['fetch_report'] == 1) {
                    $this->data['countdowns']['warrior']['finished']+= 1;
                }
                else if($countdown > $date_now && $key['fetch_report'] == 1) {
                    $this->data['countdowns']['warrior']['training']+= 1;
                }
                if($key['mission'] == 1) {
                    $this->data['countdowns']['warrior']['mission']+= 1;
                }
                else {
                    $this->data['countdowns']['warrior']['idle']+= 1;
                }
            }
            
            $db_armymission_date = date_timestamp_get(new DateTime($this->data['army_mission']['mission_countdown']));
            $now = new DateTime();
            $string = "Armymission: %s";
            if($db_armymission_date < $date_now &&
               $this->data['army_mission']['mission'] > 0) {
                $this->data['countdowns']['warrior']['armymission'] = sprintf($string, "finished");
            }
            elseif($db_armymission_date > $date_now &&
                $this->data['army_mission']['mission'] == 0) {
                $time_left = $countdown - $date_now;
                $m = round($time_left / 60);
                $countdown = "done in " . $m . 'm';
                
                $this->data['countdowns']['warrior']['armymission'] = sprintf($string, $countdown);
            }
            else {
                $this->data['countdowns']['warrior']['armymission'] = sprintf($string, 'none');
            }
            $this->data['countdowns']['trader'] = ($this->data['trader_countdown']['assignment_id'] == 0) ?
                "none" : "assignment underway";
            $this->db->closeConn();
            if($js == false) {
                return $this->data;
            }
            else {
                get_template('countdown', $this->data['countdowns'], true);
            }
        }
    }
?>