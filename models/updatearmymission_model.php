<?php
    class updatearmymission_model extends model {
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
            
            $reward = explode("|", $row2['reward']);
            
            
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE warrior SET warrior_xp=:warrior_xp, mission=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_warrior_xp = $warrior_xp + 100;
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE user_levels SET warrior_xp=:warrior_xp WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":warrior_xp", $param_warrior_xp, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_warrior_xp and $param_username is already defined in statement 1
                $stmt2->execute();
                
                $sql3 = "UPDATE warriors SET mission=0 WHERE username=:username";
                $stmt3 = $this->db->conn->prepare($sql3);
                $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
                //$param_username already is already defined in statement 1
                $stmt3->execute();
                
                update_inventory($this->db->conn, $this->username, $reward[0], $reward[1]);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
            $_SESSION['gamedata']['warrior']['warrior_xp'] = $param_warrior_xp;
        }
    }
?>