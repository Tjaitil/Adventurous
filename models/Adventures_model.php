<?php
    class Adventures_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function getCountdown() {
            $sql = "SELECT adventure_countdown, adventure_status FROM adventures WHERE :username IN (farmer, miner, trader, warrior)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['adventure_countdown']);
            $date = date_timestamp_get($datetime);
            $row['adventure_countdown'] = $date;
            js_echo($row);
        }
        
        public function checkAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            return $stmt->rowCount();
        }
        public function getData() {
            $data = array();
            $data['username'] = $this->username;
            $data['profiency'] = $this->session['profiency'];
            $profiencies = array("farmer", "miner", "warrior", "trader");
            if (array_search($data['profiency'], $profiencies) === false) {
                $this->gameMessage("ERROR: Something unexpected happened, please try again!", true);
                return false;
            }
            
            $data['current_adventure'] = $this->currentAdventure();
            
            $sql2 = "SELECT adventure_id, difficulty, location, farmer, miner, trader, warrior FROM adventures
                     WHERE adventure_status=0";
            $stmt2 = $this->db->conn->prepare($sql2);
            $param_profiency = $_SESSION['gamedata']['profiency'];
            $stmt2->execute();
            $data['pending_adventures'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $sql3 = "SELECT request_id, sender, receiver, adventure_id, role, method, request_date FROM adventure_requests
                     WHERE receiver=:username
                     ORDER BY request_date DESC LIMIT 5";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt3->execute();
            $data['requests'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
            
            return $data;
        }
        
        public function getAdventure($adventure_id) {
            $sql = "SELECT difficulty, location, farmer, miner, trader, warrior FROM adventures
                     WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            js_echo($row);
        } 
        public function currentAdventure() { 
            $data = array();
            
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $data['current'] = $row[0];
            if($data['current'] != 0) {
                $sql = "SELECT adventure_id, difficulty, location, adventure_leader, farmer, miner, trader, warrior, adventure_status,
                        other_invite
                        FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_id, PDO::PARAM_STR);
                $param_id = $data['current'];
                $stmt->execute();
                $data['info'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql = "SELECT role, required, amount, provided, status FROM adventure_requirements WHERE adventure_id=:adventure_id AND
                role IN ('farmer', 'miner', 'trader', 'warrior')
                        ORDER BY role ASC";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $param_adventure_id = $data['current'];
                $stmt->execute();
                $data['requirements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if($data['info']['trader'] != 'none') {
                    $trader_requirement = array_filter($data['requirements'], function ($key) {
                        return ($key['role'] === 'trader');
                    });
                
                    if(count($trader_requirement) > 0) {
                        $location = $data['info']['location'];
                        $sql = "SELECT {$location} FROM diplomacy WHERE username=:username";
                        $stmt = $this->db->conn->prepare($sql);
                        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                        $param_username = $data['info']['trader'];
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_NUM);
                        $data['trader_diplomacy'] = $row[0];
                        
                        $trader_index = array_keys($trader_requirement);
                        $trader_requirement = array_values($trader_requirement);
                        $trader_requirement[0]['provided'] = $data['trader_diplomacy'];
                        $data['requirements'][$trader_index[0]] = $trader_requirement[0];
                        
                        if($trader_requirement[0]['status'] != 1
                           && intval($trader_requirement[0]['amount']) <= intval($data['trader_diplomacy'])) {
                            $sql = "UPDATE adventure_requirements SET status=1 WHERE username=:username";
                            $stmt = $this->db->conn->prepare($sql);
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                            $param_username = $data['info']['trader'];
                            $stmt->execute();
                        }
                        else if($trader_requirement[0]['status'] == 1
                                 && intval($trader_requirement[0]['amount']) > intval($data['trader_diplomacy'])) {
                            $sql = "UPDATE adventure_requirements SET status=0 WHERE username=:username";
                            $stmt = $this->db->conn->prepare($sql);
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                            $param_username = $this->username;
                            $stmt->execute();
                        }
                    }
                }
                if($this->username == $data['info']['warrior']) {
                    $data['warriors'] = array();
                    $sql = "SELECT w.warrior_id, w.type, wl.stamina_level, wl.technique_level, wl.precision_level, wl.strength_level
                            FROM warriors as w
                            INNER JOIN warrior_levels as wl ON wl.warrior_id = w.warrior_id AND wl.username = w.username
                            WHERE w.mission=0 AND w.training_type='none' AND w.username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $query_array = array_column($row, 'warrior_id');
                    array_unshift($query_array, $this->username);
                    $in  = str_repeat('?,', count($query_array) - 2) . '?';
                    
                    $sql = "SELECT
                        (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                        (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence
                        FROM warrior_armory
                        WHERE username= ? AND warrior_id IN ($in)";
                        $stmt = $this->db->conn->prepare($sql);
                        $stmt->execute($query_array);
                        $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        foreach($row2 as $key => $value) {
                            array_push($data['warriors'], array_merge($row[$key], $row2[$key]));
                        }
                    
                }
                /*if(in_array(0, $data['requirements']) == false && $data['info']['adventure_status'] == 0 ) {
                    $sql = "UPDATE adventures SET adventure_status=1 WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                    $param_adventure_id = $data['current'];
                    $stmt->execute();
                    $data['info']['adventure_status'] = 1;
                }*/
            }
            return $data;
        }
        public function checkUser($username) {
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            echo $row['profiency'];
        }
        public function leaveAdventure() {
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['adventure_id'] == 0) {
                $this->gameMessage("ERROR: You are currently not in an adventure", true);
                return false;
            }
            
            $sql = "SELECT adventure_leader, farmer, miner, trader, warrior, adventure_status
                    FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $param_adventure_id = $row['adventure_id'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['adventure_status'] == 1) {
                $this->gameMessage("ERROR: You can't not leave an adventure that has started", true);
                return false;
            }
            if($row2['adventure_leader'] == $this->username) {
                $remaining = array_filter(array($row2['farmer'], $row2['miner'], $row2['trader'], $row2['warrior']), function ($var) {
                    return $var != 'none' && $var != $this->username;
                });
                if(count($remaining) > 0) {
                    $new_adventure_leader = $remaining[array_rand($remaining)];    
                }
                else {
                    $new_adventure_leader = 'none';
                }
            }
            
            
            $role = array_search($this->username, array("farmer" => $row2['farmer'], "miner" => $row2['miner'], "trader" =>
                                                        $row2['trader'], "warrior" => $row2['warrior']));
            
            $sql = "SELECT username, required, provided FROM adventure_requirements WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $requirements = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                if($role == 'warrior' && $requirements['provided'] > 0) {
                    $sql = "UPDATE warriors SET mission=0 WHERE mission=1 AND username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                }
                
                $sql = "DELETE FROM adventure_requirements WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE adventure SET adventure_id=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                if($row2['adventure_leader'] == $this->username && $new_adventure_leader != 'none') {
                    $sql = "UPDATE adventures SET adventure_leader=:adventure_leader, {$role}='none' WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                    $param_adventure_leader = $new_adventure_leader;
                    $param_adventure_id = $row['adventure_id'];
                    $stmt->execute();
                }
                else if($row2['adventure_leader'] == $this->username && $new_adventure_leader == 'none') {
                    $sql = "DELETE FROM adventures WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                    $param_adventure_id = $row['adventure_id'];
                    $stmt->execute();
                }
                else {
                    $sql = "UPDATE adventures SET {$role}='none' WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                    $param_adventure_id = $row['adventure_id'];
                    $stmt->execute();
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->gameMessage("You have left the adventure", true);
        }
    }
?>