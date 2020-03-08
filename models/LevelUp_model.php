<?php
    class LevelUp_model extends model {
        public $username;
        public $session;
        public $profiency;
        public $new_levels = array();
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function updateData() {
            //Update levels
            $profiencies = array('farmer'. 'miner', 'warrior', 'trader', 'adventurer');
            $new_level = array();
            if(in_array($this->session['level_up'], $profiencies) != false) {
                return false;
            }
            $sql = "SELECT level, next_level FROM level_data WHERE next_level > :xp ORDER BY next_level ASC LIMIT 1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":xp", $param_xp);
            foreach($this->session['level_up'] as $key => $value) {
                $param_xp = $this->session[$value]['xp'];
                $stmt->execute();
                $level = $stmt->fetch(PDO::FETCH_OBJ)->level;
                // $value is the profiency
                $this->new_levels[] = array($value, $level);
            }
            try {
                $this->db->conn->beginTransaction();
                for($i = 0; $i < count($this->session['level_up']); $i++) {
                    $sql = "UPDATE user_levels SET {$this->new_levels[$i][0]}" . "_level=:level WHERE username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_level = $this->new_levels[$i][1];
                    $param_username = $this->username;
                    $stmt->execute();
                }
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }

            /*foreach($this->session['level_up'] as $key) {
                $sql = "SELECT next_level FROM level_data WHERE level=:level"; 
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
                $param_level = $new_level[$key];
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                echo sprintf($format, $key, $new_level[$key]);
                $_SESSION['gamedata'][$key]['level'] = $new_level[$key];
                $_SESSION['gamedata'][$key]['next_level'] = $row['next_level'];
                if($key == $this->session['profiency']) {
                    $_SESSION['gamedata']['profiency_xp'] = $this->session[$key]['xp'];
                    $_SESSION['gamedata']['profiency_xp_nextlevel'] = $row['next_level'];
                }
            }*/
            $this->levelupData();
        }
        public function levelupData() {
            //Get data for the level that you have unlocked
            $data = array();
            
            $sql = "SELECT image, unlocked FROM levelup_data WHERE profiency= ? AND level= ?";
            $stmt = $this->db->conn->prepare($sql);

            $i = 0;
            foreach($this->new_levels as $key) {
                $stmt->execute(array($key[0], $key[1]));
                $data[$i] = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $data[$i]['content'][] = $row;        
                }
                $data[$i]['skill'] = $key[0];
                $data[$i]['level'] = $key[1];
                $i++;
            }
            get_template('levelUp', $data, true);
        }
    }
?>