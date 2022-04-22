<?php
    class LevelUp_model extends model {
        public $username;
        public $session;
        public $profiency;
        public $new_levels = array();
        protected $db;
        
        function __construct ($session, $db) {
            $this->username = $session['username'];
            $this->session = $session;
            $this->db = $db;
        }
        public function updateData() {
            // Update levels
            $profiencies = array('farmer'. 'miner', 'warrior', 'trader', 'adventurer');
            $new_level = array();
            if(in_array($this->session['level_up'], $profiencies) !== false) {
                var_dump($profiencies);
                throw new Exception($this->username, "Not valid skill: " . $this->session['level_up'] . __METHOD__);

                return false;
            }
            $sql = "SELECT level, next_level FROM level_data WHERE next_level > :xp ORDER BY next_level ASC LIMIT 1";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":xp", $param_xp);
            $param_xp = 1000;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->session['level_up'] = array_unique($this->session['level_up']);
            foreach($this->session['level_up'] as $key => $value) {
                // $value is the profiency name, because key is the index in the array
                $param_xp = $this->session[$value]['xp'];
                $stmt->execute();
                
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // $value is the profiency
                $this->new_levels[] = array($value, $row['level'], $row['next_level']);
            }
            foreach($this->new_levels as $key) {
                $sql = "UPDATE user_levels SET {$key[0]}_level=:level WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_level = $key[1];
                $param_username = $this->username;
                $stmt->execute();
            }            
            // Select next_level for leveled up profiencies which prevents the message from repeating
            $i = 0;
            for($i = 0; $i < count($this->new_levels); $i++) {
                $skill = $this->new_levels[$i][0];
                $level = $this->new_levels[$i][1];
                $next_level = $this->new_levels[$i][2];
                $_SESSION['gamedata'][$skill]['level'] = $level;
                $_SESSION['gamedata'][$value]['next_level'] = $next_level;
                if($value == $this->session['profiency']) {
                    $_SESSION['gamedata']['profiency_xp'] = $this->session[$value]['xp'];
                    $_SESSION['gamedata']['profiency_xp_nextlevel'] = $next_level;
                }
                echo "levelup" . "#" . $skill . "#" . $level . "#";
            }
            $_SESSION['gamedata']['level_up'] = array();
            /*$this->levelupData();*/
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