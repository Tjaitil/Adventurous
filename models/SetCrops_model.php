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
            
            $sql = "SELECT f.fields_avail, f.grow_type, fw.avail_workforce, fw.efficiency_level
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON fw.username = f.username
                    WHERE f.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row['fields_avail'] < $POST['quantity']) {
                $this->gameMessage("ERROR: You don't have that many fields available", true);
                return false;
            }
            if($row['avail_workforce'] < $POST['workforce']) {
                $this->gameMessage("ERROR: You don't have that many workers available", true);
                return false;
            }
            
            $sql = "SELECT farmer_level, experience, time, seed_required FROM crops_data
                    WHERE crop_type=:crop_type AND farmer_level<=:farmer_level AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $stmt->bindParam(":farmer_level", $param_farmer_level, PDO::PARAM_INT);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_crop_type = $POST['type'];
            $param_farmer_level = $this->session['farmer']['level'];
            $param_location = $this->session['location'];
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to grow this crop!", true);
                return false;
            }
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $POST['type'] . ' seed';
            $param_username = $this->username;
            $stmt->execute();
            $data['inventory_seed'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($data['inventory_seed'] < $row2['seed_required']) {
                $this->gameMessage("ERROR: You don't have enough seeds!", true);
                return false;
            }
            
            $addTime = $row2['time'] * $POST['quantity'] / $POST['workforce'] - (10 * $row['effiency_level']);
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            try {
                $this->db->conn->beginTransaction();
                
                $sql = "UPDATE farmer SET fields_avail=:fields_avail, grow_type=:grow_type,
                        grow_quant=:grow_quant, grow_countdown=:grow_countdown, plot1_harvest='true'
                        WHERE location=:location AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_INT);
                $stmt->bindParam(":grow_type", $param_grow_type, PDO::PARAM_STR);
                $stmt->bindParam(":grow_quant", $param_grow_quant, PDO::PARAM_INT);
                $stmt->bindParam(":grow_countdown", $param_grow_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_fields_avail = $row['fields_avail'] - $POST['quantity'];
                $param_grow_type = $POST['type'];
                $param_grow_quant = $POST['quantity'];
                $param_grow_countdown = date_format($newDate, "Y-m-d H:i:s");
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
                $this->UpdateGamedata->updateInventory($POST['type'] . ' seed', -$row2['seed_required'], true);
                // Only gain xp when warrior level is below 30 or if profiency is farmer
                if($this->session['farmer']['level'] < 30 || $this->session['profiency'] == 'farmer') { 
                    $this->UpdateGamedata->updateXP('farmer', $row2['experience']);
                }
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
            js_echo(array($param_avail_workforce, $param_fields_avail));
        }
    }
?>