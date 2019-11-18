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
            //AJAX function
            $sql = "SELECT grow_type, grow_quant, fields_avail FROM farmer WHERE username=:username AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_username = $this->username;
            $param_location = $this->session['location'];
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $crop_type = $row['grow_type'];
            
            $sql2 = "SELECT experience, min_crop_count, max_crop_count FROM crops_data WHERE crop_type=:crop_type";
            $stmt2 = $this->db->conn->prepare($sql2);
            $stmt2->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $param_crop_type = $crop_type;
            $stmt2->execute();
            $row2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            
            $experience = $row2['experience'] * $row['grow_quant'];
            $total_xp = $experience + $this->session['farmer']['xp'];
            $rand_min = ($row['grow_quant'] * 0.3) + $row2['min_crop_count'];;
            $rand_max = ($row['grow_quant'] * 0.3) + $row2['max_crop_count'];
            $quantity = round(rand($rand_min, $rand_max));
            $artefact_bonus = $this->Artefact_model->artefactCheck();
            $quantity *= $artefact_bonus;
            
            if(in_array($this->session['location'], array('towhar', 'krasnur')) != true) {
                return false;   
            }
            $workforce = $this->session['location'] . '_workforce';
            
            $sql3 = "SELECT $workforce, avail_workforce FROM farmer_workforce WHERE username=:username";
            $stmt3 = $this->db->conn->prepare($sql3);
            $stmt3->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt3->execute();
            $row3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            try {
                $this->db->conn->beginTransaction();
                
                if($artefact_bonus > 1) {
                    $this->ArtefactModel->updateArtefact();
                }
                
                $sql = "UPDATE farmer SET fields_avail=:fields_avail, grow_type=:grow_type,
                        grow_quant=:grow_quant, plot1_harvest='false' WHERE username=:username AND location=:location";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_STR);
                $stmt->bindParam(":grow_type", $param_grow_type, PDO::PARAM_STR);
                $stmt->bindParam(":grow_quant", $param_grow_quant, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $param_fields_avail = $row['fields_avail'] + $row['grow_quant'];
                $param_grow_type = 'none';
                $param_grow_quant = 0;
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
                    $this->UpdateGamedata->updateXP('farmer', $total_xp);
                }
    
                $this->UpdateGamedata->updateInventory($crop_type, $quantity, true);
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>