<?php
    class UpdateTraining_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function updateTraining($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from armycamp.js
            // Function to update the data of the warrior who has completed training
            $warrior_id = $POST['warrior_id'];
            $sql = "SELECT training_type FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $count = $stmt->rowCount();
            if(!$count > 0) {
                $this->gameMessage("ERROR: Your soldier is not currently under training", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $training_type = $row['training_type'];
                    
            
            $parameters = array();
            switch($training_type) {
                case 'general':
                    $select_SQL = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp FROM warriors_levels WHERE warrior_id=:warrior_id
                                    AND username=:username";
                    $update_SQL = "UPDATE warriors_level SET stamina_xp=:stamina_xp, technique_xp=:technique_xp, precision_xp=:precision_xp,
                                  strength_xp=:strength_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['stamina_xp'] = rand(10,20);
                    $parameters['technique_xp'] = rand(10,20);
                    $parameters['precision_xp'] = rand(10,20);
                    $parameters['strength_xp'] = rand(10,20);
                    break;
                
                 case 'stamina':
                    $select_SQL = "SELECT stamina_xp FROM warriors_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL = "UPDATE warriors_level SET stamina_xp=:stamina_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['stamina_xp'] = rand(25,35);
                    break;
                
                case 'technique':
                    $select_SQL = "SELECT technique_xp FROM warriors_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL ="UPDATE warriors_level SET technique_xp=:technique_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['technique_xp'] = $experience = rand(25,35);
                    break;
                
                case 'precision':
                    $select_SQL= "SELECT precision_xp FROM warriors_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $sql ="UPDATE warriors_level SET precision_xp=:precision_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['precision_xp'] = rand(25,35);
                    break;
                    
                case 'strength':
                    $select_SQL = "SELECT strength_xp FROM warriors_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL ="UPDATE warrior_level SET strength_xp=:strength_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['strength_xp'] = rand(25,35);
                    break;
            }
            
            $sql = $select_SQL;
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $warrior_DBXP = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            //Make the statement
            $sql_update = "UPDATE warriors_levels SET";
                foreach ($parameters as $key => $value) {
                    $sql_update .= ' ' . $key .  '=:' . $key . ',';
                    $values[':'.$key] = $value + $warrior_DBXP[$key];
                }
            $sql_update = rtrim($sql_update,",");
            $sql_end = " WHERE warrior_id=:warrior_id AND username=:username";
            $sql4 = $sql_update . $sql_end;
            $values[':warrior_id'] = $warrior_id;
            $values[':username'] = $this->username;
            
            $sql = "SELECT experience FROM training_type_data WHERE training_type=:training_type";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
            $param_training_type = $training_type;
            $stmt->execute();
            $warrior_experience = $stmt->fetch(PDO::FETCH_OBJ)->experience;
        
            try {
                $this->db->conn->beginTransaction();
                
                $sql2 = "UPDATE warriors SET fetch_report=0, training_type='none' WHERE warrior_id=:warrior_id AND username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt2->execute();
                
                // Only gain xp when warrior level is below 30 or if profiency is warrior
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                    $this->UpdateGamedata->updateXP('warrior', $warrior_experience);
                }
                
                $stmt4 = $this->db->conn->prepare($sql4);
                $stmt4->execute($values);

                $this->db->conn->commit();
                if($training_type === 'general') {
                    $this->gameMessage("You have trained your soldier and gotten
                                       {$parameters['stamina_xp']} stamina xp,
                                       {$parameters['technique_xp']} technique xp,
                                       {$parameters['precision_xp']} precision xp and
                                       {$parameters['strength_xp']} strength xp", true);    
                }
                else {
                    $this->gameMessage("You have trained your soldier and gotten {$parameters[$training_type]} .
                                       {$training_type} xp", true);    
                }
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>