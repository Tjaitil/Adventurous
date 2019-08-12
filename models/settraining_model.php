<?php
    class settraining_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getTrainingTypeData($trainingType, $warrior_id) {
            $sql = "SELECT time, experience FROM training_type_data WHERE training_type=:training_type";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
            $param_training_type = $trainingType;
            $stmt->execute();
            $data = array();
            $data['training_type'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT warrior_id FROM warriors WHERE warrior_id=:warrior_id AND location=:location AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_location = $this->session['location'];
            $param_username = $this->username;
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to train this soldier!");
                return false;
            }
            unset($stmt);
            return $data;
        }
        
        public function setTrainingData($warrior_data) {
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE warrior SET warrior_xp=:warrior_xp WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":warrior_xp", $param_miner_xp, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_xp = $warrior_data['user_experience'] + $warrior_data['warrior_xp'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE warriors SET training_type=:training_type, training_countdown=:training_countdown, fetch_report=1
                        WHERE warrior_id=:warrior_id AND username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
                $stmt2->bindParam(":training_countdown", $param_training_countdown, PDO::PARAM_STR);
                $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_training_type = $warrior_data['type'];
                $param_training_countdown = $warrior_data['training_countdown'];
                $param_warrior_id = $warrior_data['id'];
                $param_username = $this->username;
                $stmt2->execute();
                
                update_xp($this->conn, $this->username, 'warrior', $warrior_data['user_experience'] + $warrior_data['warrior_xp']);
                
                $this->conn->commit();
                }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again");
                return false;
            }
            $_SESSION['gamedata']['warrior']['warrior_xp'] = $param_warrior_xp;
            $this->closeConn();
        }
    }
?>