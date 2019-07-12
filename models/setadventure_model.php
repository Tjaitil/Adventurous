<?php
    class setadventure_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
         public function newAdventure($adventure_data) {
            $categories = array();
            
            
            $profiences = array('farmer', 'miner', 'trader', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                print "ERROR";
                return false;
            }
            try {
                $this->conn->beginTransaction();
                
                $sql = "INSERT INTO adventures (adventure_leader) VALUES (:adventure_leader)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":adventure_leader", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $adventure_id = $this->conn->lastInsertId();
                
                $sql2 = "UPDATE current_adventure SET adventure_id=:adventure_id WHERE username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_adventure_id = $adventure_id;
                $param_username = $this->username;
                $stmt2->execute();
                
                $sql3 = "UPDATE adventures SET difficulty=:difficulty, location=:location, " .
                         $this->session['profiency'] . "=:profiency"  . " WHERE adventure_id=:adventure_id AND
                         adventure_leader=:adventure_leader";
                $stmt3 = $this->conn->prepare($sql3);
                $stmt3->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
                $stmt3->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt3->bindParam(":profiency", $param_profiency, PDO::PARAM_STR);
                $stmt3->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt3->bindParam(":adventure_leader", $param_adventure_leader, PDO::PARAM_STR);
                $param_difficulty = $adventure_data['difficulty'];
                $param_location = $adventure_data['location'];
                $param_profiency = $this->username;
                $param_adventure_id = $adventure_id;
                $param_adventure_leader = $this->username;
                $stmt3->execute();
                
                $sql4 = "INSERT INTO adventures_" . $this->session['profiency'] . " (adventure_id, username)
                         VALUES(:adventure_id, :username)";
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt4->bindParam(":username", $param_username, PDO::PARAM_STR);
                // $param_adventure_id is already defined
                $param_username = $this->username;
                $stmt4->execute();
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            echo "new adventure";
            $this->closeConn();
        }
        
        public function provide($adventure_id, $route, $item, $quantity, $warrior_check) {
            if($this->session['profiency'] == 'trader') {
                $this->gameMessage("The trader role doesn't have to provide with anything");
                return false;
            }
            $profiences = array('farmer', 'miner', 'warrior');
            if(array_search($this->session['profiency'], $profiences) === false) {
                $this->gameMessage("ERROR");
                return false;
            }
            $sql = "SELECT adventure_id FROM adventure WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['adventure_id'] != $adventure_id) {
                $this->gameMessage("ERROR: This adventure doesn't exists!");
                return false;
            }
            $sql = "SELECT adventure_id, difficulty, location FROM adventures WHERE adventure_id=adventure_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR");
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT required, amount FROM adventure_requirments WHERE difficulty=:difficulty AND location=:location AND role=:role";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":difficulty", $param_difficulty, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":role", $param_role, PDO::PARAM_STR);
            $param_difficulty = $row['difficulty'];
            $param_location = $row['location'];
            $param_role = $this->session['profiency'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT provided FROM adventures_" . $this->session['profiency'] . "
                       WHERE adventure_id=:adventure_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_adventure_id = $adventure_id;
            $param_username = $this->username;
            $stmt->execute();
            $row3 = $stmt->fetch(PDO::FETCH_ASSOC);
                
            if($row3['provided'] == $row2['amount']) {
                $this->gameMessage("You have provided enough!");
                return false;
            }
            
            if($route == 'item') {
                if($row2['required'] != $item) {
                $this->gameMessage("ERROR: You are trying to provide the wrong item");
                return false;
                }
            
                $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $this->username;
                $stmt->execute();
                $row4 = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row4['amount'] < $quantity) {
                    print "ERROR, not enough in inventory!";
                    return false;
                }
            }     
            else if ($route == 'warrior') {
                $queryArray = explode(",", $warrior_check);
                if(count($queryArray) > $row2['amount']) {
                    $this->gameMessage("You are trying to provide more warriors than you have to");
                    return false;
                }
                $queryArray[] = $this->username;
                $in  = str_repeat('?,', count($queryArray) - 2) . '?';
                $sql = "SELECT warrior_id FROM warriors WHERE warrior_id IN ($in) AND fetch_report=0 AND username=?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($queryArray);
                $row4 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(!$stmt->rowCount() > 0) {
                    $this->gameMessage("Check again if your soldiers are active or not!");
                    return false;
                }
                $quantity = count($row4);
                $row4['amount'] = count($row4);
            }
            
            $missing_contribution = $row2['amount'] - $row3['provided']; 
            if($missing_contribution < $quantity) {
                $quantity = $missing_contribution; //If the player is trying to provide more than is requires
            }
            $missing_contribution = $missing_contribution - $quantity;
            ($missing_contribution == 0)? $status = 1: $status = 0;
            try {
                $this->conn->beginTransaction(); 
                
                $sql = "UPDATE adventures_" . $this->session['profiency'] . " SET provided=:provided, status=:status
                        WHERE adventure_id=:adventure_id AND username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":provided", $param_provided, PDO::PARAM_STR);
                $stmt->bindParam(":status", $param_status, PDO::PARAM_STR);
                $stmt->bindParam(":adventure_id", $param_adventure_id, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_provided = $quantity + $row3['provided'];
                $param_status = $status;
                $param_adventure_id = $adventure_id;
                $param_username = $this->username;
                $stmt->execute();
                if($route == 'item') {
                    require_once('../' . constant("ROUTE_HELPER") . 'update_inventory.php');
                    update_inventory($this->conn, $this->username, $item, -$quantity);
                }
                else if ($route == 'warrior') {
                    $sql2 = "UPDATE warriors SET mission=1 WHERE warrior_id IN ($in) AND username=?";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->execute($queryArray);
                }
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $string = "You have provided %s";
            $this->gameMessage(sprintf($string, $quantity), true);
            js_echo(array($this->session['profiency'], $param_provided, $missing_contribution, $status));
            $data = get_inventory($this->conn, $this->username);
            js_foreach($data);
        }
    }
?>