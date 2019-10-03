<?php
    class LevelUp_model extends model {
        public $username;
        public $session;
        public $profiency;
        
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
                $new_level[$value] = $stmt->fetch(PDO::FETCH_OBJ)->level;
            }
            try {
                $this->db->conn->beginTransaction();
                foreach($this->session['level_up'] as $key) {
                    $sql = "UPDATE user_levels SET $key" . "_level=:level WHERE username=:username";         
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_level = $new_level[$key];
                    $param_username = $this->username;
                    $stmt->execute();
                    
                }
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }

            unset($stmt, $stmt2);
            $format = "You have leveled up %s to %d!";
            foreach($this->session['level_up'] as $key) {
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
            }
            /*$this->levelupData();*/
        }
        
        public function levelupData() {
            //Get data for the level that you have unlocked
            $sql = "SELECT images, unlocked FROM levelup_data WHERE level=:level AND profiency=:profiency";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
            $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
            $param_level = $this->newLevel;
            $param_profiency = $_SESSION['gamedata']['profiency'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $images = explode("|", $row['images']);
            $unlocked = explode("|", $row['unlocked']);
            $this->db->closeConn();
            js_echo($images);
            echo "unlocked|";
            js_echo($unlocked);
        }
    }
?>