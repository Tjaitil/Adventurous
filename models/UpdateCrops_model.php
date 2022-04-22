<?php
    class UpdateCrops_model extends model {
        public $username;
        public $session;
        
        function __construct($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, true);
        }
        public function updateCrops() {
            // Function to reset crops, free up workforce and give player the amount of crops they have grown
            // This function is called from an AJAX request from crops.js
            
            $sql = "SELECT crop_type, crop_quant FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $crop_type = $row['crop_type'];
            
            if($row['crop_type'] === "none") {
                $this->gameMessage("ERROR: You don't have any crops to harvest", true);
                return false;
            }
            
            $sql2 = "SELECT experience, min_crop_count, max_crop_count FROM crops_data WHERE crop_type=:crop_type";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $param_crop_type = $crop_type;
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            $sql3 = "SELECT avail_workforce, {$this->session['location']}_workforce, mineral_quant_level
            FROM miner_workforce WHERE username=:username";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
         
            $rand_min = $row2['min_crop_count'] * 0.3 + $row3[$this->session['location'] . '_workforce'];
            $rand_max = $row2['max_crop_count'] * 0.3 + $row3[$this->session['location'] . '_workforce'];
            $quantity = round(rand($rand_min, $rand_max));
            $experience = $row2['experience'] + (round($row2['experience'] / 100 * $quantity));
            
            // If the player has harvester artefact equipped, get harvester bonus
            if(strpos($this->session['artefact'], 'harvester') !== false) {
                $artefact_bonus = $this->ArtefactModel->artefactCheck('harvester');
            }
            else {
                $artefact_bonus = 1;
            }
            $quantity = round($quantity * $artefact_bonus);
            
            
            try {
                $this->db->conn->beginTransaction();
                
                if($artefact_bonus > 1) {
                    $this->ArtefactModel->updateArtefact();
                }
                echo "|";
                
                $sql = "UPDATE farmer SET fields_avail=:fields_avail, crop_type=:crop_type, plot1_harvest=0 
                        WHERE username=:username AND location=:location";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_STR);
                $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
                $stmt->bindParam(":crop_quant", $param_crop_quant, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $param_crop_type = 'none';
                $param_crop_quant = 0;
                $param_username = $this->username;
                $param_loation = $this->session['location'];
                $stmt->execute();
                
                $sql2 = "UPDATE farmer_workforce SET avail_workforce=:avail_workforce, $workforce= 0
                         WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_avail_workforce = $row3['avail_workforce'] + $row3[$workforce];
                $param_username = $this->username;
                $stmt2->execute();
                // Only gain xp when farmer level is below 30 or if profiency is farmer
                if($this->session['farmer']['level'] < 30 || $this->session['profiency'] == 'farmer') { 
                    $this->UpdateGamedata->updateXP('farmer', $experience);
                    $xpUpdate = true;
                }
    
                $this->UpdateGamedata->updateInventory($crop_type, $quantity, true);
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            /* Echo order, split by "|"
             * [0] -> possible artefact use
             * [1] -> possible level up message;
             * [2] -> gameMessage
             * [3] -> $echo_data with updated game data
             */
            echo "|";
            $echo_data = array();
            if(isset($xpUpdate)) {
                $echo_data['xp_gained'] = $experience;
                $this->gameMessage("Harvested crops, {$xpUpdate} famer xp gained", true);
            }
            echo "|";
            $echo_data['avail_workforce'] = $param_avail_workforce;
            
            echo json_encode($echo_data);
        }
    }
?>