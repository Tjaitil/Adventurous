<?php
    class UpdateArmymission_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        public function updateMission() {
            $warrior_xp = $_SESSION['gamedata']['warrior']['warrior_xp'];
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
                
                
                // Only gain xp when warrior level is below 30 or if profiency is farmer
                if($this->session['warrior']['level'] < 30 || $this->session['profiency'] == 'warrior') { 
                    update_xp($this->db->conn, $this->username, 'warrior', 100);
                }
                
                $sql3 = "UPDATE warriors SET mission=0 WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already is already defined in statement 1
                $stmt3->execute();
                
                update_inventory($this->db->conn, $this->username, 'gold', $row2['reward']);
                
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