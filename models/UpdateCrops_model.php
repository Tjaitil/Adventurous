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
            $location_workforce = $this->session['location'] . '_workforce';
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $sql = "SELECT crop_type, crop_quant FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $crop_type = $row['crop_type'];
            
            if($row['crop_type'] === "none") {
                $this->response->addTo("errorGameMessage", "You don't have any crops to harvest");
                return false;
            }
            
            $param_crop_type = $crop_type;
            $sql2 = "SELECT experience, min_crop_count, max_crop_count FROM crops_data WHERE crop_type=:crop_type";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);

            $sql3 = "SELECT avail_workforce, $location_workforce
                     FROM farmer_workforce WHERE username=:username";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
         
            $rand_min = $row2['min_crop_count'] * (0.3 + $row3[$this->session['location'] . '_workforce']);
            $rand_max = $row2['max_crop_count'] * (0.3 + $row3[$this->session['location'] . '_workforce']);
            $quantity = round(rand($rand_min, $rand_max));
            $experience = $row2['experience'] + (round($row2['experience'] / 100 * $quantity));
            
            // If the player has harvester artefact equipped, get harvester bonus
            // if(strpos($this->session['artefact'], 'harvester') !== false) {
            //     $artefact_bonus = $this->ArtefactModel->artefactCheck('harvester');
            // }
            // else {
            //     $artefact_bonus = 1;
            // }
            // $quantity = round($quantity * $artefact_bonus);
            
            try {
                $this->db->conn->beginTransaction();
                
                // if($artefact_bonus > 1) {
                //     $this->response->addTo("gameMessage", $this->ArtefactModel->updateArtefact());
                // }
                $param_crop_type = 'none';
                $param_username = $this->username;
                $param_location = $this->session['location'];
                $sql = "UPDATE farmer SET crop_type=:crop_type, plot1_harvest=0 
                        WHERE username=:username AND location=:location";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_avail_workforce = intval($row3['avail_workforce']) + intval($row3[$location_workforce]);
                $param_username = $this->username;
                $sql2 = "UPDATE farmer_workforce SET avail_workforce=:avail_workforce, $location_workforce=0
                         WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->execute();
                // Only gain xp when farmer level is below 30 or if profiency is farmer
                if($this->session['farmer']['level'] < 30 || $this->session['profiency'] == 'farmer') { 
                    $this->response->addTo("levelUP", $this->UpdateGamedata->updateXP('farmer', $experience));
                }
    
                $this->UpdateGamedata->updateInventory($crop_type, $quantity, true);
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("gameMessage", "Harvested crops, {$experience} famer xp gained", false);
            $this->response->addTo("data", $param_avail_workforce, array("index" => "availWorkforce"));
        }
    }
?>