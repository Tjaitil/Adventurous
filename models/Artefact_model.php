<?php
    class Artefact_model extends model {
        public $username;
        public $session;
        private $artefacts = array("harvester", "prospector", "collector", "fighter", "healer", "rewardist");
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function newArtefact() {
            $crystals = array('hirtam crystal', 'pvitul crystal', 'khanz crystal', 'ter crystal', 'fansal crystal');
            $queryArray = $crystals;
            $queryArray[] = $this->username;
            $in  = str_repeat('?,', count($crystals) - 1) . '?';

            $sql = "SELECT amount FROM inventory
                    WHERE item IN ($in)
                    AND username = ?"; //
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute($queryArray);
            if($stmt->rowCount() < 5) {
                $this->gameMessage("ERROR: You are missing on ore more crystals!", true);
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            
            $new_artefact = $this->artefacts[array_rand($this->artefacts)];
            
            try {
                $this->db->conn->beginTransaction();
                
                for($i = 0; $i < count($crystals); $i++) {
                    $this->updateGamedata->updateInventory($crystals[$i], -1);
                }
                $this->updateGamedata->updateInventory($new_artefact . ' (10)', 1, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            // Date should not be included in this message so $this->gameMessage() is not used, check citycentre.js
            js_echo(array("You received a new artefact: ", ucfirst($new_artefact)));
        }
        public function changeArtefact($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request from citycentre.js
            // Function to change the active artefact for user
            $artefact = $POST['artefact'];
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $artefact;
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(!$stmt->rowCount() > 0 || $row['amount'] == 0) {
                $this->gameMessage("ERROR: You don't have that artefact in your inventory", true);
                return false;
            }
            
            $sql = "SELECT artefact FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $old_artefact = $stmt->fetch(PDO::FETCH_OBJ)->artefact;
            
            if($stmt->rowCount() > 0) {
                $preg = preg_match('#\((.*)\)#', $old_artefact, $matches);
                $charges = $matches[1];
                $artefact_sub = explode("(", $old_artefact)[0];
                $old_artefact = $artefact_sub . "({$charges})";
            }
            else {
                $charges = 0;
            }
            if(strpos($artefact, "(") == false) {
                $artefact = $artefact . " (10)";
            }
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE user_data SET artefact=:artefact WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":artefact", $param_artefact, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_artefact = $artefact;
                $param_username = $this->username;
                $stmt->execute();
                
                if($charges > 0) {
                    // Update inventory
                    $this->UpdateGamedata->updateInventory($old_artefact, 1);
                }
                // Update inventory
                $this->UpdateGamedata->updateInventory($artefact, -1, true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            $_SESSION['gamedata']['artefact'] = $artefact;
            js_echo(array($artefact));
        }
        public function artefactCheck($type) {
            preg_match('#\((.*)\)#', $this->session['artefact'], $matches);
            if($matches[1] == 0) {
                return 1;
            }
            $artefact = trim(explode("(", $this->session['artefact'])[0]);
            if($artefact !== $type) {
                return 1;
            }
            switch($artefact) {
                case 'harvester':
                    return 1.2;
                    break;
                case 'prospector':
                    return 1.1;
                    break;
                case 'collector':
                    return 1.05;
                    break;
                case 'fighter':
                    return 10/15;
                    break;
                case 'healer':
                    return;
                    break;
                case 'rewardist':
                    return 1.2;
                    break;
                default:
                    return 1;
                    break;
            }
        }
        public function updateArtefact() {
            $sql = "SELECT artefact FROM user_data WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $artefact = $stmt->fetch(PDO::FETCH_OBJ)->artefact;
            
            preg_match('#\((.*)\)#', $artefact, $matches);
            $charges = $matches[1] - 1;
            $artefact_sub = trim(explode("(", $artefact)[0]);
            
                
            $sql = "UPDATE user_data SET artefact=:artefact WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":artefact", $param_artefact, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_artefact = $artefact_sub . " ({$charges})";
            $param_username = $this->username;
            $stmt->execute();
            
            $_SESSION['gamedata']['artefact'] = $param_artefact;
            $this->gameMessage($this->session['artefact'] . " artefact used, charges left: {$charges}", true);
            echo "|";
        }
    }
?>