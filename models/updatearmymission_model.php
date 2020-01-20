<?php
    class UpdateArmymission_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function updateMission() {
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT reward FROM armymissions WHERE mission_id=:mission_id";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":mission_id", $param_mission_id, PDO::PARAM_STR);
            $param_mission_id = $row['mission'];
            $stmt->execute();
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warrior SET mission=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_INT);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql3 = "UPDATE warriors SET mission=0 WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already is already defined in statement 1
                $stmt3->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', $row2['reward']);
                // Update xp
                $this->UpdateGamedata->updateXP('warrior', 100);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
        public function cancelMission() {
            $sql = "SELECT mission FROM warrior WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $stmt->fetch(PDO::FETCH_ASSOC);
            
            try {
                
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warrior SET mission=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                
                $sql = "UPDATE warriors SET mission=0 WHERE mission=mission AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":mission", $param_mission, PDO::PARAM_INT);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_mission = $row['mission'];
                $param_username = $this->username;
                $stmt->execute();
                $stmt->fetch(PDO::FETCH_ASSOC);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>