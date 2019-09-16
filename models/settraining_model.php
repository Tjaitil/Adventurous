<?php
    class SetTraining_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getTrainingTypeData($trainingType, $warrior_id) {
           
            unset($stmt);
            return $data;
        }
        
        public function setTraining($warrior, $type) {
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND location=:location AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior;
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to train this soldier!");
                return false;
            }
            
            $sql = "SELECT time, experience FROM training_type_data WHERE training_type=:training_type";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
            $param_training_type = $type;
            $stmt->execute();
            $data = array();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $addTime = $row2['time'];
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$addTime} seconds");
            $user_experience = $this->session['warrior']['xp'] + $row2['experience'];
            
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE warrior SET warrior_xp=:warrior_xp WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":warrior_xp", $param_miner_xp, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_xp = $row2['experience'] + $this->session['warrior']['xp'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE warriors SET training_type=:training_type, training_countdown=:training_countdown, fetch_report=1
                         WHERE warrior_id=:warrior_id AND username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
                $stmt2->bindParam(":training_countdown", $param_training_countdown, PDO::PARAM_STR);
                $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_training_type = $type;
                $param_training_countdown = date_format($new_date, "Y-m-d H:i:s");
                $param_warrior_id = $warrior;
                $param_username = $this->username;
                $stmt2->execute();
                
                update_xp($this->conn, $this->username, 'warrior', $user_experience);
                
                $this->conn->commit();
                }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            $_SESSION['gamedata']['warrior']['warrior_xp'] = $user_experience;
            $this->closeConn();
            $date = date_timestamp_get(new DateTime($param_training_countdown));
            js_echo(array($date, $type));
        }
    }
?>