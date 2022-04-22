<?php
    class SetCrops_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function setCrops($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set crops
            $POST = json_decode($POST['JSON_data'], true);
            
            if($this->session['hunger'] < 10) {
                $this->gameMessage("ERROR: Your hunger is too high, please eat!", true);
                return false;
            }
            
            $sql = "SELECT f.fields_avail, f.crop_type, fw.avail_workforce, fw.efficiency_level
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON fw.username = f.username
                    WHERE f.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            /*if($row['fields_avail'] < $POST['quantity']) {
                $this->gameMessage("ERROR: You don't have that many fields available", true);
                return false;
            }*/
            if($row['avail_workforce'] < $POST['workforce']) {
                $this->gameMessage("ERROR: You don't have that many workers available", true);
                return false;
            }
            
            $sql = "SELECT farmer_level, experience, time, seed_required FROM crops_data
                    WHERE crop_type=:crop_type AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_crop_type = $POST['crop'];
            $param_farmer_level = $this->session['farmer']['level'];
            $param_location = $this->session['location'];
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to grow this crop!", true);
                return false;
            }
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['farmer_level'] < $this->session['farmer']['level']) {
                $this->gameMessage("ERROR: Your farmer level is too low to grow this crop", true);
                return false;
            }
            
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $POST['crop'] . ' seed';
            $param_username = $this->username;
            $stmt->execute();
            $data['inventory_seed'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($data['inventory_seed'] < $row2['seed_required']) {
                $this->gameMessage("ERROR: You don't have enough seeds!", true);
                return false;
            }
            
            $addTime = $row2['time'] - (($row2['time'] / 100) * ($POST['effiency_level'] / 100));
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE farmer SET crop_type=:crop_type,
                        crop_quant=:crop_quant, crop_countdown=:crop_countdown, plot1_harvest=1
                        WHERE location=:location AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_INT);
                $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
                $stmt->bindParam(":crop_quant", $param_crop_quant, PDO::PARAM_INT);
                $stmt->bindParam(":crop_countdown", $param_crop_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_crop_type = $POST['crop'];
                $param_crop_quant = 1;
                $param_crop_countdown = date_format($newDate, "Y-m-d H:i:s");
                $param_location = $this->session['location'];
                $param_username = $this->username;
                $stmt->execute();
                
                $sql2 = "UPDATE farmer_workforce SET avail_workforce=:avail_workforce,
                                {$this->session['location']}_workforce=:crop_workforce WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":crop_workforce", $param_crop_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_avail_workforce = $row['avail_workforce'] - $POST['workforce'];
                $param_crop_workforce = $POST['workforce'];
                $param_username = $this->username;
                $stmt2->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($POST['crop'] . ' seed', -$row2['seed_required'], true);
                // Only gain xp when warrior level is below 30 or if profiency is farmer
                if($this->session['farmer']['level'] < 30 || $this->session['profiency'] == 'farmer') { 
                    $this->UpdateGamedata->updateXP('farmer', $row2['experience']);
                    $xpUpdate = true;
                }
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            /* Echo order, split by "|"
             * [0] -> possible level up message;
             * [1] -> gameMessage
             * [2] -> $echo_data with updated game data
             */
            echo "|";
            $echo_data = array();
            if(isset($xpUpdate)) {
                $echo_data['xp_gained'] = $experience;
                $this->gameMessage("Planted crops, {$xpUpdate} famer xp gained", true);
            }
            echo "|";
            $echo_data['avail_workforce'] = $param_avail_workforce;
            echo json_encode($echo_data);
        }
    }
?>