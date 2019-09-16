<?php
    class RecruitWorker_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        public function recruitWorker($type, $level = false) {
            // AJAX function
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
            }
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt2 = $this->conn->prepare($sql2);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            
            if($row[$column] == $row2[$column2]) {
                $this->gameMessage("ERROR: You need to level up before recruiting more", true);
                return false;
            }
            if($type == 'warrior') {
                $sql = "SELECT MAX(warrior_id) FROM warriors WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                if($level > 1) {
                    $sql4 = "SELECT next_level FROM warriors_level_data WHERE skill_level=:level";
                    $stmt4 = $this->conn->prepare($sql4);
                    $stmt4->bindParam(":level", $param_level, PDO::PARAM_STR);
                    $param_level = $level - 1;
                    $stmt4->execute();
                    $warrior_xp = $stmt4->fetch(PDO::FETCH_OBJ)->next_level;
                }
                else {
                    $warrior_xp = 0;
                }
            }
            $sql = "SELECT price FROM tavern_prices WHERE type=:type";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $param_type = $type;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['price'] > $this->session['gold']) {
                $this->gameMessage("ERROR: You don't have enough gold", true);
                return false;
            }
        
            $sql = "SELECT type, level FROM tavern_workers WHERE city=:city AND username=:username AND level=:level";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $stmt->bindParam(":level", $param_level, PDO::PARAM_INT); 
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $param_level = $level;
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: The worker you are trying to buy does not exists!", true);
                return false;
            }
            $row4 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->conn->beginTransaction();
                
                if(in_array($type, array('farmer'. 'miner')) == true) {
                    $sql = "UPDATE {$type}_workforce SET workforce_total=:workforce_total, avail_workforce=:avail_workforce
                            WHERE username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":workforce_total", $param_workforce_total, PDO::PARAM_STR);
                    $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_workforce_total = $row2['workforce_total'] + 1;
                    $param_avail_workforce = $row2['avail_workforce'] + 1;
                    $param_username = $this->username;
                    $stmt->execute();
                }
                else {
                    $sql = "UPDATE warrior SET warrior_amount=:warrior_amount WHERE username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":warrior_amount", $param_warrior_amount, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_warrior_amount = $row2['warrior_amount'] + 1;
                    $param_username = $this->username;
                    $stmt->execute();
                
                    $sql2 = "INSERT INTO warriors (username, warrior_id, type)
                            VALUES(:username, :warrior_id, :type)";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                    $stmt2->bindParam(":type", $param_type, PDO::PARAM_STR);
                    $param_username = $this->username;
                    $param_warrior_id = $row3['MAX(warrior_id)'] + 1;
                    $param_type = $type;
                    $stmt2->execute();
                    
                    $sql3 = "INSERT INTO warrior_levels (username, warrior_id, stamina_level,  stamina_xp, technique_level, technique_xp.
                             precision_level, precision_xp, strength_level, strength_xp)
                             VALUES(:username, :warrior_id, :stamina_level, :stamina_xp, :technique_level, :technique_xp, :precision_level,
                             precision_xp, :strength_level, :strength_xp)";
                    $stmt3 = $this->conn->prepare($sql3);   
                    $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt3->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                    $stmt3->bindParam(":stamina_level", $param_level, PDO::PARAM_INT);
                    $stmt3->bindParam(":stamina_xp", $param_xp, PDO::PARAM_INT);
                    $stmt3->bindParam(":technique_level", $param_level, PDO::PARAM_INT);
                    $stmt3->bindParam(":technique_xp", $param_xp, PDO::PARAM_INT);
                    $stmt3->bindParam(":precision_level", $param_level, PDO::PARAM_INT);
                    $stmt3->bindParam(":precision_xp", $param_xp, PDO::PARAM_INT);
                    $stmt3->bindParam(":strength_level", $param_level, PDO::PARAM_INT);
                    $stmt3->bindParam(":strength_xp", $param_xp, PDO::PARAM_INT);
                    // $param_username and $param_warrior_id is already defined in statement 1
                    $param_level = $level;
                    $param_xp = $warrior_xp;
                    $stmt3->execute();
                    
                    $sql4 = "INSERT INTO warrior_armory (username, warrior_id) VALUES(:username, :warrior_id)";
                    $stmt4 = $this->conn->prepare($sql4);
                    $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt4->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                    // $param_username and $param_warrior_id is already defined in statement 1
                    $stmt4->execute();
                }
                
                update_inventory($this->conn, $this->username, 'gold', - $row['price'], true);

                $sql5 = "DELETE FROM tavern_workers WHERE city=:city AND type=:type AND level=:level LIMIT 1";
                $stmt5 = $this->conn->prepare($sql5);
                $stmt5->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt5->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt5->bindParam(":level", $param_level, PDO::PARAM_STR);
                $param_city = $this->session['location'];
                // $param_type defined in statement 2 and $param_level defined in statement 3;
                $stmt5->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            if(in_array($type, array('melee', 'ranged')) == true) {
                $this->gameMessage("You just recruited a {$type} warrior", true);
            }
            else {
                $this->gameMessage("You just recruited a {$type}, true", true);
            }
        }
    }
?>