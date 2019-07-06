<?php
    class adventures_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getCountdown() {
            $sql = "SELECT adventure_countdown, adventure_status FROM adventures WHERE (farmer OR miner OR trader OR warrior)=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['adventure_countdown']);
            $date = date_timestamp_get($datetime);
            $row['adventure_countdown'] = $date;
            $this->closeConn();
            js_echo($row);
        }
        
        public function checkAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $this->closeConn();
            return $stmt->rowCount();
        }
        
        public function getData() {
            $data = array();
            $data['profiency'] = $this->session['profiency'];
            $profiencies = array("farmer", "miner", "warrior", "trader");
            if (array_search($data['profiency'], $profiencies) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            $data['current_adventure'] = $this->currentAdventure();
            
            $sql2 = "SELECT adventure_id, difficulty, location, farmer, miner, trader, warrior FROM adventures
                     WHERE " . $data['profiency'] . "='none' AND adventure_status=0";
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
            $param_profiency = $_SESSION['gamedata']['profiency'];
            $stmt2->execute();
            $data['pending_adventures'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT request_id, sender, receiver, adventure_id, role, method FROM adventure_requests WHERE method = 'sender' OR method='receiver'
                     AND sender=:username OR receiver=:username";
            $stmt3 = $this->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['requests'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            $this->closeConn();
            return $data;
        }
        
        public function getAdventure($adventure_id) {
            $sql = "SELECT difficulty, location, farmer, miner, trader, warrior FROM adventures
                     WHERE adventure_id=:adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            js_echo($row);
        } 
        public function currentAdventure() { 
            $data = array();
            
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $data['current'] = $row[0];
            if($data['current'] != 0) {
                $sql = "SELECT adventure_id, difficulty, location, adventure_leader, farmer, miner, trader, warrior, adventure_status
                        FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_id, PDO::PARAM_STR);
                $param_id = $data['current'];
                $stmt->execute();
                $data['info'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT provided, status FROM adventures_farmer WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $data['info']['adventure_id'];
                $param_username = $data['info']['farmer'];
                $stmt->execute();
                $data['farmer'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT provided, status FROM adventures_miner WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $data['info']['adventure_id'];
                $param_username = $data['info']['miner'];
                $stmt->execute();
                $data['miner'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT provided, status FROM adventures_trader WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $data['info']['adventure_id'];
                $param_username = $data['info']['trader'];
                $stmt->execute();
                $data['trader'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT provided, status FROM adventures_warrior WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $data['info']['adventure_id'];
                $param_username = $data['info']['warrior'];
                $stmt->execute();
                $data['warrior'] = $stmt->fetch(PDO::FETCH_ASSOC);
                var_dump($data['warrior']);
                
                $sql = "SELECT required, amount FROM adventure_requirments WHERE location=:location AND difficulty=:difficulty";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $param_location = $data['info']['location'];
                $param_difficulty = $data['info']['difficulty'];
                $stmt->execute();
                $data['requirements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $location = $data['info']['location'];
                $sql = "SELECT " .  $location . " FROM diplomacy WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $data['info']['trader'];
                var_dump($data['info']['trader']);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $data['trader_diplomacy'] = $row[0];
                if($data['trader']['status'] == 0 || $data['requirements'][3]['required'] >= $data['trader_diplomacy']) {
                    $sql = "UPDATE adventures_trader SET status=1 WHERE username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $data['info']['trader'];
                    $stmt->execute();
                }
                if($this->username == $data['info']['warrior']) {
                    $sql = "SELECT warrior_id, type FROM warriors WHERE mission=0 AND training_type='none' AND username=:username"; //FIKS
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $warriors = array();
                    $queryArray = array();
                    $i = 0;
                    foreach($row as $key) {
                        array_push($queryArray, $key['warrior_id']);
                        $warriors[$key['warrior_id']] = $row[$i];
                        $i++;
                    }
                    
                    $queryArray[] = $this->username;
                    if($row != false) {
                    $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                    $sql = "SELECT warrior_id, stamina_level, technique_level, precision_level, strength_level
                            FROM warrior_levels WHERE warrior_id IN ($in) AND username=?";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->execute($queryArray);
                    $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    foreach($row2 as $key) {
                        $parts = array_merge($key, $warriors[$key['warrior_id']]);
                        $warriors[$key['warrior_id']] = $parts;
                    }
                    }
                    $data['warriors'] = $warriors;
                }
            }
            $data['username'] = $this->username;
            return $data;
        }
        
        public function checkUser($username) {
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->closeConn();
            echo $row['profiency'];
        }
    }
?>