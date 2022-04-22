<?php
    class Adventures_model extends model {
        public $username;
        public $session;
        public $current_adventure = array();
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function getCountdown() {
            $param_username = $this->username;
            $sql = "SELECT adventure_countdown, adventure_status FROM adventures WHERE :username IN (farmer, miner, trader, warrior)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $datetime = new DateTime($row['adventure_countdown']);
            $date = date_timestamp_get($datetime);
            $row['adventure_countdown'] = $date;
        $this->response->addTo("data", $row['adventure_countdown'], array("index" => "adventure_countdown"));
            $this->response->addTo("data", $row['adventure_status'], array("index" => "adventure_status"));
        }
        
        public function checkAdventure() {
            $param_username = $this->username;
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->rowCount();
        }
        public function getData() {
            $data = array();
            $data['username'] = $this->username;
            $data['profiency'] = $this->session['profiency'];
            
            $this->currentAdventure();
            
            $sql2 = "SELECT adventure_id, difficulty, location, farmer, miner, trader, warrior FROM adventures
            WHERE adventure_status=0";
            $stmt2 = $this->db->conn->prepare($sql2);
            $this->current_adventure['adventures']= $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $stmt2->execute();
            $this->current_adventure['join_adventures'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            
            $param_username = $this->username;
            $this->current_adventure['requests'] = array();
            if($this->current_adventure['adventure_id'] !== 0) {
                $sql3 = "SELECT request_id, sender, receiver, adventure_id, role, method, request_date 
                        FROM adventure_requests 
                        WHERE receiver=:username AND method='request' AND
                        EXISTS(SELECT adventure_id FROM adventures 
                        WHERE adventure_requests.adventure_id = adventures.adventure_id) 
                        ORDER BY request_date DESC LIMIT 5";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt3->execute();
                if($stmt3->rowCount() > 0) {
                    $this->current_adventure['requests'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            $this->current_adventure['invites'] = array();
            if($this->current_adventure['adventure_id'] !== 0) {
                $sql3 = "SELECT request_id, receiver, adventure_id, role, method, request_date 
                        FROM adventure_requests 
                        WHERE sender=:username AND method='invite' AND
                        EXISTS(SELECT adventure_id FROM adventures 
                        WHERE adventure_requests.adventure_id = adventures.adventure_id) 
                        ORDER BY request_date DESC LIMIT 5";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt3->execute();
                if($stmt3->rowCount() > 0) {
                    $this->current_adventure['invites'] = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                }
            }
            return $this->current_adventure;
        }
        public function getAdventure($GET) {
            $param_adventure_id = $GET['adventure_id'];
            $sql = "SELECT difficulty, location, farmer, miner, trader, warrior FROM adventures
                     WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            js_echo($row);
        } 
        public function currentAdventure() { 
            $data = array();
            
            $param_username = $this->username;
            $sql = "SELECT adventure_id, notification FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->current_adventure['adventure_id'] = $row['adventure_id'];
            $this->current_adventure['adventure_data'] = $row;
            if($this->current_adventure['adventure_id'] !== 0) {
                $param_id = $this->current_adventure['adventure_id'];
                $sql = "SELECT difficulty, location, adventure_leader, farmer, miner, trader, warrior, adventure_status,
                        other_invite
                        FROM adventures WHERE adventure_id=:adventure_id";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_id, PDO::PARAM_STR);
                $stmt->execute();
                $this->current_adventure['info'] = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $param_adventure_id = $this->current_adventure['adventure_id'];
                $sql = "SELECT role, required, amount, provided, status FROM adventure_requirements WHERE adventure_id=:adventure_id AND
                role IN ('farmer', 'miner', 'trader', 'warrior')
                        ORDER BY role ASC";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->execute();
                $this->current_adventure['requirements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if(in_array($this->current_adventure['info']['trader'], array('none', 'citizen')) === false) {
                    $trader_requirement = array_filter($this->current_adventure['requirements'], function ($key) {
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
                        $this->current_adventure['trader_diplomacy'] = $row[0];
                        
                        $trader_index = array_keys($trader_requirement);
                        $trader_requirement = array_values($trader_requirement);
                        $trader_requirement[0]['provided'] = $this->current_adventure['trader_diplomacy'];
                        $this->current_adventure['requirements'][$trader_index[0]] = $trader_requirement[0];
                        
                        if($trader_requirement[0]['status'] != 1
                           && intval($trader_requirement[0]['amount']) <= intval($this->current_adventure['trader_diplomacy'])) {
                            $sql = "UPDATE adventure_requirements SET status=1 WHERE username=:username";
                            $stmt = $this->db->conn->prepare($sql);
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                            $param_username = $this->current_adventure['info']['trader'];
                            $stmt->execute();
                        }
                        else if($trader_requirement[0]['status'] == 1
                                 && intval($trader_requirement[0]['amount']) > intval($this->current_adventure['trader_diplomacy'])) {
                            $sql = "UPDATE adventure_requirements SET status=0 WHERE username=:username";
                            $stmt = $this->db->conn->prepare($sql);
                            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                            $param_username = $this->username;
                            $stmt->execute();
                        }
                    }
                }
                if($this->username === $this->current_adventure['info']['warrior']) {
                    $this->current_adventure['warriors'] = array();
                    $sql = "SELECT w.warrior_id, w.type, wl.stamina_level, wl.technique_level, wl.precision_level, wl.strength_level
                            FROM warriors as w
                            INNER JOIN warriors_levels as wl ON wl.warrior_id = w.warrior_id AND wl.username = w.username
                            WHERE w.mission=2 AND w.training_type='none' AND w.username=:username";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if($stmt->rowCount() > 0) {
                        $query_array = array_column($row, 'warrior_id');
                        array_unshift($query_array, $this->username);
                        $in  = str_repeat('?,', count($query_array) - 2) . '?';
                        $data['warriors'] = array();
                        $sql = "SELECT
                            (SELECT SUM(attack) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS attack,
                            (SELECT SUM(defence) FROM armory_items_data WHERE item IN (helm, left_hand, body, right_hand, boots)) AS defence,
                            helm, ammunition, ammunition_amount, body, right_hand, left_hand, legs, boots
                            FROM warrior_armory
                            WHERE username= ? AND warrior_id IN ($in)";
                            $stmt = $this->db->conn->prepare($sql);
                            $stmt->execute($query_array);
                            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach($row2 as $key => $value) {
                                array_push($data['warriors'], array_merge($row[$key], $row2[$key]));
                            }
                    }
                }
            }
        }
        public function checkUser($GET) {
            $param_username = $GET['$username'];
            $sql = "SELECT profiency FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->db->closeConn();
            echo $row['profiency'];
        }
        public function leaveAdventure() {
            $param_username = $this->username;
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['adventure_id'] == 0) {
                $this->response->addTo("errorGameMessage", "You are currently not in an adventure");
                return false;
            }
            
            $param_adventure_id = $row['adventure_id'];
            $sql = "SELECT adventure_leader, farmer, miner, trader, warrior, adventure_status
                    FROM adventures WHERE adventure_id=:adventure_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['adventure_status'] == 1) {
                $this->response->addTo("errorGameMessage", "You can't not leave an adventure that has started");
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
                    $param_adventure_leader = $new_adventure_leader;
                    $param_adventure_id = $row['adventure_id'];
                    $sql = "UPDATE adventures SET adventure_leader=:adventure_leader, {$role}='none' WHERE adventure_id=:adventure_id";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                    $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
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
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->response->addTo("gameMessage", "You have left the adventure");
        }
    }
?>