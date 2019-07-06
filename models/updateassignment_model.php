<?php
    class updateassignment_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function updateAssignment($favor = false) {
            if($favor != true) {
                $sql = "SELECT cart_amount, trading_countdown FROM trader WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $sql2 = "SELECT amount FROM stockpile WHERE item='gold' AND username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->execute();
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $date = date("Y-m-d H:i:s");
                $trading_countdown = new DateTime($date);
            }
            
            $datetime = date_create();
            $date_now = date_timestamp_get($datetime);
            $trading_countdown = new DateTime($row['trading_countdown']);
            $date_assignment = date_timestamp_get($trading_countdown);
            if($date_assignment > $date_now) {
                $time_left = $date_assignment - $date_now;
                // Calculate the time that is left, 2xp for every minute
                $xp =  $time_left / 0.0083;
            }
            else {
                $xp = 0;
            }
            if($favor = true) {
                $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $diplomacy = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "diplomacy:";
                echo "</br>";
                var_dump($diplomacy);
                
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM city_relations WHERE city=:city";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
                $param_city = $this->session['favor']['base'];
                var_dump($this->session['favor']);
                $stmt->execute();
                $city_relations = $stmt->fetch(PDO::FETCH_ASSOC);
                
                $new_diplomacy = array();
                for($i = 0; $i < count($diplomacy); $i++) {
                    echo intval($diplomacy[$locations[$i]]) . '*' . $city_relations[$locations[$i]];
                    echo $city_relations[$locations[$i]];
                    echo "</br>";
                    $new_diplomacy[$locations[$i]] = intval($diplomacy[$locations[$i]]) * $city_relations[$locations[$i]];
                }
                var_dump($new_diplomacy);
            }
            
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE trader SET trader_xp=:trader_xp, assignment_amount=0, assignment_id=0, delivered=0
                        WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_trader_xp = $xp + $this->session['trader']['xp'];
                $param_username = $this->username;
                $stmt->execute();
                
                if($favor != true) {
                    $sql2 = "UPDATE user_levels SET trader_xp=:trader_xp WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":trader_xp", $param_trader_xp, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    // $param_trader_xp already defined in statement 1
                    $param_username = $this->username;
                    $stmt2->execute();
                    require_once('../' . constant("ROUTE_HELPER") . 'updatestockpile.php');
                    $stockpile = updatestockpile($this->conn, $this->username, 'gold' , $row2['amount']);
                }
                else {
                    $sql2 = "UPDATE diplomacy SET hirtam=:hirtam, pvitul=:pvitul, khanz=:khanz, ter=:ter, fansalplains=:fansalplains
                        WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":hirtam", $param_Hirtam, PDO::PARAM_STR);
                    $stmt2->bindParam(":pvitul", $param_Pvitul, PDO::PARAM_STR);
                    $stmt2->bindParam(":khanz", $param_Khanz, PDO::PARAM_STR);
                    $stmt2->bindParam(":ter", $param_Ter, PDO::PARAM_STR);
                    $stmt2->bindParam(":fansalplains", $param_FansalPlains, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_Hirtam = $new_diplomacy['hirtam'];
                    $param_Pvitul = $new_diplomacy['pvitul'];
                    $param_Khanz = $new_diplomacy['khanz'];
                    $param_Ter = $new_diplomacy['ter'];
                    $param_FansalPlains = $new_diplomacy['fansalplains'];
                    $param_username = $this->username;
                    $stmt2->execute();
                    
                    $sql3 = "UPDATE trader_assignments SET base='none', destination='none', cargo='none', cargo_amount='none'
                        WHERE assignment_id=1"; 
                    $this->conn->query($sql3);
                    unset($_SESSION['gamedata']['favor']);
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
            $_SESSION['gamedata']['trader']['xp'] = $param_trader_xp;
            if($favor != true) {
                $this->gameMessage("XP bonus for finishing assignment before deadline", true); 
            }
            else {
                $this->gameMessage("You have finsihed your favor assignment", true); 
            }
        }
    }
?>