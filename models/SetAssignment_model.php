<?php
    class SetAssignment_model extends model {
        public $username;
        public $session;
        public $destination;
        public $favor;
        public $assignment_type;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            if(isset($this->session['favor'])) {
                $this->destination = $this->session['favor']['destination'];
                $this->favor = $this->session['favor'];    
            }
            $this->assignment_type = restore_file('trader_assignment_types', true);
            $this->commonModels(true, false);
        }
        public function newAssignment($POST, $favor = false) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set a new trader assignment
            $assignment_id = $POST['assignment_id'];
            if($this->session['hunger'] < 10) {
                $this->gameMessage("ERROR: Your hunger is too high, please eat!", true);
                return false;
            }
            /*$assignment_amount = str_replace(" ", "+", $assignment_amount);*/
            $sql = "SELECT assignment_id, cart FROM trader WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
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
            $sql = "SELECT destination, assignment_amount, time, assignment_type FROM trader_assignments WHERE assignment_id=:assignment_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
            $param_assignment_id = $assignment_id;
            $stmt->execute();
            $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
            if($favor != true) {
                $sql2 = "SELECT xp, time FROM assignment_types WHERE type=:type";
                $stmt2 = $this->db->conn->prepare($sql2);
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
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE trader SET assignment_id=:assignment_id, trading_countdown=:trading_countdown WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":assignment_id", $param_id, PDO::PARAM_STR);
                $stmt->bindParam(":trading_countdown", $param_trading_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_id = $assignment_id;
                $param_trading_countdown = date_format($new_date, "Y-m-d H:i:s");
                $param_username = $this->username;
                $stmt->execute();
                
                // Only gain xp when warrior level is below 30 or if profiency is trader and assignment_xp is greater than 0
                if($assignment_XP > 0 && ($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader')) {
                    $this->UpdateGamedata->updateXP('trader', $assignment_XP);    
                }
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $this->gameMessage("New assignment taken", true);
        }
    }
?>