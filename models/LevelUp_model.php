<?php
    class LevelUp_model extends model {
        public $username;
        public $session;
        public $profiency;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
            $this->profiency = $_SESSION['gamedata']['profiency'];
        }
        
        public function updateData() {
            //Update levels
            $profiencies = array('farmer'. 'miner', 'warrior', 'trader', 'adventurer');
            $new_level = array();
            $levels = array();
            if(in_array($this->session['level_up'], $profiencies) != false) {
                return false;
            }
            
            foreach($this->session['level_up'] as $key => $value) {
                array_push($levels, $this->session[$value]['xp']);
            }
            $in  = str_repeat('?,', count($levels) - 1) . '?';
            $sql = "SELECT level, next_level FROM level_data WHERE next_level IN ($in) < next_level";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($levels);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            var_dump($row);
            
            foreach($this->session['level_up'] as $key => $value) {
                $new_level[$value] = $r  
            }
            
            var_dump($this->session['level_up']);
            var_dump($new_level);
            /*try {
                $this->conn->beginTransaction();
                foreach($this->session['level_up'] as $key) {
                    $sql = "UPDATE user_levels SET $key" . "_level=:level WHERE username=:username";         
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_level = $new_level[$key];
                    $param_username = $this->username;
                    $stmt->execute();
                    
                }
                $this->conn->commit();
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }

            unset($stmt, $stmt2);
            $format = "You have leveled up %s to %d!";
            foreach($this->session['level_up'] as $key) {
                $sql = "SELECT next_level FROM level_data WHERE level=:level"; 
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
                $param_level = $new_level[$key];
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->gameMessage(sprintf($format, $key, $new_level[$key]), true);
                $_SESSION['gamedata'][$key]['level'] = $new_level[$key];
                $_SESSION['gamedata'][$key]['next_level'] = $row['next_level'];
            }
            /*$this->levelupData();*/
        }
        
        public function levelupData() {
            //Get data for the level that you have unlocked
            $sql = "SELECT images, unlocked FROM levelup_data WHERE level=:level AND profiency=:profiency";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
            $stmt->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
            $param_level = $this->newLevel;
            $param_profiency = $_SESSION['gamedata']['profiency'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $images = explode("|", $row['images']);
            $unlocked = explode("|", $row['unlocked']);
            $this->closeConn();
            js_echo($images);
            echo "unlocked|";
            js_echo($unlocked);
        }
    }
?>