<?php
    class SetTraining_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, true);
        }
        public function setTraining($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set training

            $warriors = $POST['warrior'];
            $type = $POST['type'];
            
            $query_array = explode(",", $warriors);
            $warrior_ids = explode(",", $warriors);
            $amount = count($query_array);
            $in  = str_repeat('?,', count($query_array) - 1) . '?';
            $query_array[] = $this->username;
            $query_array[] = $this->session['location'];

            $sql = "SELECT warrior_id FROM warriors
            WHERE fetch_report=0 AND mission=0 AND warrior_id IN ($in) AND username=? AND location=?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($query_array);
            if($stmt->rowCount() !== $amount) {
                $this->response->addTo("errorGameMessage" , "One or more of the selected warriors are on mission or training");
                return false;
            }
            
            $param_training_type = $type;
            $sql = "SELECT time, experience FROM training_type_data WHERE training_type=:training_type";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $addTime = $row2['time'];
            $date = date("Y-m-d H:i:s");
            $new_date = new DateTime($date);
            $new_date->modify("+{$addTime} seconds");
            
            try {
                $this->db->conn->beginTransaction();
                $param_training_type = "";
                $param_training_countdown = "";
                $param_warrior_id = "";
                $param_username = $this->username;
                $sql2 = "UPDATE warriors SET training_type=:training_type, training_countdown=:training_countdown, fetch_report=1
                         WHERE warrior_id=:warrior_id AND username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
                $stmt2->bindParam(":training_countdown", $param_training_countdown, PDO::PARAM_STR);
                $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                foreach($warrior_ids as $key) {
                    $param_training_type = $type;
                    $param_training_countdown = date_format($new_date, "Y-m-d H:i:s");
                    $param_warrior_id = $key;
                    $stmt2->execute();
                }
                
                // Only gain xp when warrior level is below 30 or if profiency is warrior
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                    $this->response->addTo("levelUP", 
                        $this->UpdateGamedata->updateXP('warrior', $row2['experience'] * count($warriors))
                    );
                }
                $this->db->conn->commit();
                }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Warrior {$type} training started");
            $this->response->send();
        }
    }
