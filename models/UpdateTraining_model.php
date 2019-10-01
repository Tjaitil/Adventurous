<?php
    class UpdateTraining_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function updateTraining($warrior_id) {
            $sql = "SELECT training_type FROM warriors WHERE warrior_id=:warrior_id AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $count = $stmt->rowCount();
            if(!$count > 0) {
                echo "ERROR!";
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $training_type = $row['training_type'];
                    
            
            $parameters = array();
            switch($training_type) {
                case 'general':
                    $select_SQL = "SELECT stamina_xp, technique_xp, precision_xp, strength_xp FROM warrior_levels WHERE warrior_id=:warrior_id
                                    AND username=:username";
                    $update_SQL = "UPDATE warriors_level SET stamina_xp=:stamina_xp, technique_xp=:technique_xp, precision_xp=:precision_xp,
                                  strength_xp=:strength_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['stamina_xp'] = rand(10,20);
                    $parameters['technique_xp'] = rand(10,20);
                    $parameters['precision_xp'] = rand(10,20);
                    $parameters['strength_xp'] = rand(10,20);
                    break;
                
                 case 'stamina':
                    $select_SQL = "SELECT stamina_xp FROM warrior_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL = "UPDATE warriors_level SET stamina_xp=:stamina_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['stamina_xp'] = rand(25,35);
                    break;
                
                case 'technique':
                    $select_SQL = "SELECT technique_xp FROM warrior_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL ="UPDATE warriors_level SET technique_xp=:technique_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['technique_xp'] = $experience = rand(25,35);
                    break;
                
                case 'precision':
                    $select_SQL= "SELECT precision_xp FROM warrior_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $sql ="UPDATE warriors_level SET precision_xp=:precision_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['precision_xp'] = rand(25,35);
                    break;
                    
                case 'strength':
                    $select_SQL = "SELECT strength_xp FROM warrior_levels WHERE warrior_id=:warrior_id AND username=:username";
                    $update_SQL ="UPDATE warrior_level SET strength_xp=:strength_xp WHERE warrior_id=:warrior_id AND username=:username";
                    $parameters['strength_xp'] = rand(25,35);
                    break;
            }
            
            $sql = $select_SQL;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_warrior_id = $warrior_id;
            $param_username = $this->username;
            $stmt->execute();
            $warrior_DBXP = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            //Make the statement
            $sql_update = "UPDATE warrior_levels SET";
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
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":training_type", $param_training_type, PDO::PARAM_STR);
            $param_training_type = $type;
            $stmt->execute();
            $warrior_experience = $stmt->fetch(PDO::FETCH_OBJ)->experience;
        
            try {
                $this->conn->beginTransaction();
                $sql = "UPDATE warrior SET warrior_xp=:warrior_xp WHERE username=:username";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_xp = $warrior_experience + $this->session['warrior']['xp'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE warriors SET fetch_report=0, training_type='none' WHERE warrior_id=:warrior_id AND username=:username";
                $stmt2 = $this->conn->prepare($sql2);
                $stmt2->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_id = $warrior_id;
                $param_username = $this->username;
                $stmt2->execute();
                
                // Only gain xp when warrior level is below 30 or if profiency is warrior 
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') {
                    update_xp($this->conn, $this->username, 'warrior', $param_warrior_xp);
                }
                
                $stmt4 = $this->conn->prepare($sql4);
                $stmt4->execute($values);

                $this->conn->commit();
                $this->gameMessage("You have trained your soldier", true);
            }
            catch (Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->closeConn();
        }
    }
?>