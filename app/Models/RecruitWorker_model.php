<?php

// TODO: Delete
    class RecruitWorker_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function recruitWorker($POST) {
            $type = strtolower($POST['type']);
            $level = ($POST['level'] == "false") ? 0 : $POST['level'];

            switch($type) {
                case "farmer":
                    $column = "max_farm_workers";
                    $column2 = "workforce_total";
                    $sql = "SELECT max_farm_workers FROM level_data WHERE level=:level";
                    $sql2 = "SELECT workforce_total, avail_workforce FROM farmer_workforce WHERE username=:username";
                    $param_level = $this->session['farmer']['level'];
                    break;
            
                case "miner":
                    $column = "max_mine_workers";
                    $column2 = "workforce_total";
                    $sql = "SELECT max_mine_workers FROM level_data WHERE level=:level";
                    $sql2 = "SELECT workforce_total, avail_workforce FROM miner_workforce WHERE username=:username";
                    $param_level = $this->session['miner']['level'];
                    break;
            
                case "melee":
                case "ranged":
                    $column = "max_warriors";
                    $column2 = "warrior_amount";
                    $sql = "SELECT max_warriors FROM level_data WHERE level=:level";
                    $sql2 = "SELECT warrior_amount FROM warrior WHERE username=:username";
                    $param_level = $this->session['warrior']['level'];
                    break;
                default:
                    
                    break;
            }
            if(!isset($sql)) {
                $this->response->addTo("errorGameMessage", "Something unexpected happened, please try again");
                return false;
            }
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $param_username = $this->username;
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if($row[$column] >= $row2[$column2]) {
                $this->response->addTo("errorGameMessage", "You need to level up before recruiting more");
                return false;
            }
            if($type == 'warrior') {
                $sql = "SELECT MAX(warrior_id) FROM warriors WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                if($level > 1) {
                    $sql4 = "SELECT next_level FROM warriors_level_data WHERE skill_level=:level";
                    $stmt4 = $this->db->conn->prepare($sql4);
                    $stmt4->bindParam(":level", $param_level, PDO::PARAM_STR);
                    $param_level = $level - 1;
                    $stmt4->execute();
                    $warrior_xp = $stmt4->fetch(PDO::FETCH_OBJ)->next_level;
                }
                else {
                    $warrior_xp = 0;
                }
            }
            $param_type = $type;
            $sql = "SELECT price FROM tavern_prices WHERE type=:type";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['price'] > $this->session['gold']) {
                $this->response->addTo("errorGameMessage", "You don't have enough gold");
                return false;
            }
        
            $param_city = $this->session['location'];
            $param_level = $level;
            $param_username = $this->username;
            $sql = "SELECT type, level FROM tavern_workers WHERE city=:city AND username=:username AND level=:level";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_INT); 
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "The worker you are trying to recruit does not exists!");
                return false;
            }
            $row4 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                // if(in_array($type, array('farmer', 'miner')) == true) {
                //     $param_workforce_total = $row2['workforce_total'] + 1;
                //     $param_avail_workforce = $row2['avail_workforce'] + 1;
                //     $param_username = $this->username;
                //     $sql = "UPDATE {$type}_workforce SET workforce_total=:workforce_total, avail_workforce=:avail_workforce
                //             WHERE username=:username";
                //     $stmt = $this->db->conn->prepare($sql);
                //     $stmt->bindParam(":workforce_total", $param_workforce_total, PDO::PARAM_STR);
                //     $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                //     $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                //     $stmt->execute();
                // }
                // else {
                //     $param_warrior_amount = $row2['warrior_amount'] + 1;
                //     $sql = "UPDATE warrior SET warrior_amount=:warrior_amount WHERE username=:username";
                //     $stmt = $this->db->conn->prepare($sql);
                //     $stmt->bindParam(":warrior_amount", $param_warrior_amount, PDO::PARAM_STR);
                //     $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                //     $param_username = $this->username;
                //     $stmt->execute();
                    
                //     $param_username = $this->username;
                //     $param_warrior_id = $row3['MAX(warrior_id)'] + 1;
                //     $param_type = $type;
                //     $sql2 = "INSERT INTO warriors (username, warrior_id, type)
                //             VALUES(:username, :warrior_id, :type)";
                //     $stmt2 = $this->db->conn->prepare($sql2);
                //     $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                //     $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                //     $stmt2->bindParam(":type", $param_type, PDO::PARAM_STR);
                //     $stmt2->execute();
                    
                //     $param_level = $level;
                //     $param_xp = $warrior_xp;
                //     $sql3 = "INSERT INTO warriors_levels (username, warrior_id, stamina_level,  stamina_xp, technique_level, technique_xp.
                //              precision_level, precision_xp, strength_level, strength_xp)
                //              VALUES(:username, :warrior_id, :stamina_level, :stamina_xp, :technique_level, :technique_xp, :precision_level,
                //              precision_xp, :strength_level, :strength_xp)";
                //     $stmt3 = $this->db->conn->prepare($sql3);   
                //     $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //     $stmt3->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                //     $stmt3->bindParam(":stamina_level", $param_level, PDO::PARAM_INT);
                //     $stmt3->bindParam(":stamina_xp", $param_xp, PDO::PARAM_INT);
                //     $stmt3->bindParam(":technique_level", $param_level, PDO::PARAM_INT);
                //     $stmt3->bindParam(":technique_xp", $param_xp, PDO::PARAM_INT);
                //     $stmt3->bindParam(":precision_level", $param_level, PDO::PARAM_INT);
                //     $stmt3->bindParam(":precision_xp", $param_xp, PDO::PARAM_INT);
                //     $stmt3->bindParam(":strength_level", $param_level, PDO::PARAM_INT);
                //     $stmt3->bindParam(":strength_xp", $param_xp, PDO::PARAM_INT);
                //     // $param_username and $param_warrior_id is already defined in statement 1
                //     $stmt3->execute();
                    
                //     $sql4 = "INSERT INTO warrior_armory (username, warrior_id) VALUES(:username, :warrior_id)";
                //     $stmt4 = $this->db->conn->prepare($sql4);
                //     $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                //     $stmt4->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                //     // $param_username and $param_warrior_id is already defined in statement 1
                //     $stmt4->execute();
                // }
                
                // // Update inventory
                // $this->UpdateGamedata->updateInventory('gold', - $row['price'], true);

                // $sql5 = "DELETE FROM tavern_workers WHERE city=:city AND type=:type AND level=:level LIMIT 1";
                // $stmt5 = $this->db->conn->prepare($sql5);
                // $stmt5->bindParam(":city", $param_city, PDO::PARAM_STR);
                // $stmt5->bindParam(":type", $param_type, PDO::PARAM_STR);
                // $stmt5->bindParam(":level", $param_level, PDO::PARAM_STR);
                // $param_city = $this->session['location'];
                // // $param_type defined in statement 2 and $param_level defined in statement 3;
                // $stmt5->execute();
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            
            if(in_array($type, array('melee', 'ranged')) == true) {
                $message = "You just recruited a {$type} warrior";
            }
            else {
                $message = "You just recruited a {$type}, true";
            }
            $this->response->addTo("gameMessage", $message);
        }
    }
