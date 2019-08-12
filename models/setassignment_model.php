<?php
    class setassignment_model extends model {
        public $username;
        public $session;
        public $destination;
        public $favor;
        public $assignment_type;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
            $this->destination = $this->session['favor']['destination'];
            $this->favor = $this->session['favor'];
            $this->assignment_type = restore_file('trader_assignment_types', true);
        }
        public function newAssignment($assignment_id, $favor = false) {
            /*$assignment_amount = str_replace(" ", "+", $assignment_amount);*/
            $sql = "SELECT assignment_id, cart FROM trader WHERE username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row['assignment_id'] > 0) {
                $this->gameMessage("ERROR! Finish your assignment before taking a new one", true);
                return false;
            }
            if($row['cart'] == 'none') {
                $this->gameMsssage("ERROR! You don't have a cart", true);
                return false;
            }
            if($favor != true) {
                $sql = "SELECT destination, assignment_amount, time, assignment_type FROM trader_assignments WHERE assignment_id=:assignment_id";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $assignment_id;
                $stmt->execute();
                $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            else {
                $sql = "SELECT destination, assignment_amount, time, assignment_type FROM trader_assignments
                WHERE assignment_id=:assignment_id AND assignment_type='favor'";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
                $param_assignment_id = $assignment_id;
                $stmt->execute();
                $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            if($favor != true) {
                $sql2 = "SELECT xp, time FROM assignment_types WHERE type=:type";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":type", $param_type, PDO::PARAM_STR);
                $param_type = $assignment_data['assignment_type'];
                $stmt2->execute();
                $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                $assignment_XP = $row2['xp'];
                //countdown;
                $add_time = $assignment_data['time'];
                $date = date("Y-m-d H:i:s");
                $new_date = new DateTime($date);
                $new_date->modify("+{$add_time} seconds");
            }
            else {
                $assignment_XP = 0;
                $date = date("Y-m-d H:i:s");
                $new_date = new DateTime($date);
            }
            
            try {
                $this->conn->beginTransaction();
                
                $sql = "UPDATE trader SET assignment_id=:assignment_id, trading_countdown=:trading_countdown WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":trading_countdown", $param_trading_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $assignment_id;
                $param_trading_countdown = date_format($new_date, "Y-m-d H:i:s");
                $param_username = $this->username;
                $stmt->execute();
                
                update_xp($this->conn, $this->username, 'trader', $this->session['trader']['xp'] + $assignment_XP);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
            $_SESSION['gamedata']['trader']['xp'] =  $this->session['trader']['xp'] + $assignment_XP;
            $this->gameMessage("New assignment taken", true);
        }
    }
?>