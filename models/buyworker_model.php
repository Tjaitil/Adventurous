<?php
    class buyworker_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function buyWorker($type, $type_level = false) {
            //AJAX function
            switch($type) {
                case "farmer":
                    $sql = "SELECT max_workers FROM workforcelodge_data WHERE level=:level";
                    $sql2 = "SELECT workforce_total, avail_workforce FROM farmer_workforce WHERE username=:username";
                    $param_level = $this->session['farmer']['level'];
                    break;
            
                case "miner":
                    $sql = "SELECT max_workers FROM workforcelodge_data WHERE level=:level";
                    $sql2 = "SELECT workforce_total, avail_workforce FROM miner_workforce WHERE username=:username";
                    $param_level = $this->session['miner']['level'];
                    break;
            
                case "melee":
                case "archer":
                    $sql = "SELECT max_warriors FROM armycamp_data WHERE level=:level";
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
            
            switch($type) {
                case "farmer":
                    $this->buyWorkforce($type, $row, $row2);
                    break;
                case "miner":
                    $this->buyWorkforce($type, $row, $row2);
                    break;
                case "melee" || "archer":
                    $this->buyWarrior($type, $type_level, $row, $row2);
                    break;
            }
        }
        
        public function buyWorkforce($type, $row, $row2) {
            if($row['max_workers'] <= $row2['workforce_total']) {
                    $this->gameMessage("You don't have enough space in your workforcelodge");
                    return false;
            }
            
            $sql = "SELECT price FROM tavern_prices WHERE type=:type";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $param_type = $type;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['price'] > $this->session['gold']) {
                $this->gameMessage("ERROR: You don't have enough gold");
                return false;
            }
            
            $sql = "SELECT " . $type . "_amount FROM tavern_workers WHERE city=:city AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_city = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            $row4 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE " . $type . "_workforce SET workforce_total=:workforce_total, avail_workforce=:avail_workforce
                        WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":workforce_total", $param_workforce_total, PDO::PARAM_STR);
                $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_workforce_total = $row2['workforce_total'] + 1;
                $param_avail_workforce = $row2['avail_workforce'] + 1;
                $param_username = $this->username;
                $stmt->execute();
                
                update_inventory($this->conn, $this->username, 'gold', - $row['price']);

                $sql3 = "UPDATE tavern_workers SET " . $type . "_amount=:amount WHERE city=:city AND username=:username";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt3->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_amount = $row4[$type . '_amount'] - 1;
                $param_city = $this->session['location'];
                //$param_username already defined in statement 1
                $stmt3->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $_SESSION['gamedata']['gold']['amount'] -= $row4['price'];
            $this->gameMessage("You just recruited a " . $type, true);
        }
        
        public function buyWarrior($type, $type_level, $row, $row2) {
            if($row['max_warriors'] <= $row2['warrior_amount']) {
                    $this->gameMessage("You don't have enough space in your armycamp", true);
                    return false;
            }
            $sql = "SELECT MAX(warrior_id) FROM warriors WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT price FROM tavern_prices WHERE type='warrior'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            $row4 = $stmt->fetch(PDO::FETCH_ASSOC);

            if($row4['price'] > $this->session['gold']) {
                $this->gameMessage("ERROR: You don't have enough gold", true);
                return false;
            }
            try {
                $this->conn->beginTransaction();
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
                    
                $sql3 = "INSERT INTO warrior_levels (username, warrior_id, stamina_level, technique_level, precision_level,
                strength_level) VALUES(:username, :warrior_id, :stamina_level, :technique_level, :precision_level,
                :strength_level)";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt3->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt3->bindParam(":stamina_level", $param_level, PDO::PARAM_STR);
                $stmt3->bindParam(":technique_level", $param_level, PDO::PARAM_STR);
                $stmt3->bindParam(":precision_level", $param_level, PDO::PARAM_STR);
                $stmt3->bindParam(":strength_level", $param_level, PDO::PARAM_STR);
                //$param_username and $param_warrior_id is already defined in statement 1
                $param_level = $type_level;
                $stmt3->execute();
                
                $sql4 = "INSERT INTO warrior_armory (username, warrior_id) VALUES(:username, :warrior_id)";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt4->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                //$param_username and $param_warrior_id is already defined in statement 1
                $stmt4->execute();
                
                update_inventory($this->conn, $this->username, 'gold', -$row4['price']);
                
                $sql6 = "DELETE FROM tavern_warriors WHERE city=:city AND type=:type AND level=:level LIMIT 1";
                $stmt6 = $this->conn->prepare($sql6);
                $stmt6->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt6->bindParam(":type", $param_type, PDO::PARAM_STR);
                $stmt6->bindParam(":level", $param_level, PDO::PARAM_STR);
                $param_city = $this->session['location'];
                //$param_type defined in statement 2 and $param_level defined in statement 3;
                $stmt6->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $_SESSION['gamedata']['gold']['amount'] -= $row4['price'];
            $this->closeConn();
            $this->gameMessage("You just recruited a warrior, type: " . $type, true);
        }
    }
?>