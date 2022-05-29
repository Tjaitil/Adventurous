<?php
    class SetCrops_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false, true);
        }
        public function setCrops($POST) {
            // $POST variable holds the post data
            // This function is called from an AJAX request
            // Function to set crops
            $POST = json_decode($POST['JSON_data'], true);
            
            if($this->hungerModel->checkHunger()) {
                $this->response->addTo("errorGameMessage", $this->hungerModel->getHungerError());
                return false;
            } 

            $param_username = $this->username;
            $sql = "SELECT f.crop_type, fw.avail_workforce, fw.efficiency_level
                    FROM farmer as f INNER JOIN farmer_workforce as fw ON fw.username = f.username
                    WHERE f.username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($row['avail_workforce'] < $POST['workforce']) {
                $this->response->addTo("errorGameMessage" , "You don't have that many workers available");
                return false;
            }
            $param_crop_type = $POST['crop'];
            $param_location = $this->session['location'];
            $sql = "SELECT farmer_level, experience, time, seed_required FROM crops_data
                    WHERE crop_type=:crop_type AND location=:location";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage" , "You are in the wrong city to grow this crop!");
                return false;
            }
            $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row2['farmer_level'] > $this->session['farmer']['level']) {
                $this->response->addTo("errorGameMessage" , "Your farmer level is too low to grow this crop");
                return false;
            }
            
            $param_item_seed = $POST['crop'] . ' seed';
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item_seed, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data['inventory_seed'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($data['inventory_seed'] < $row2['seed_required']) {
                $this->response->addTo("errorGameMessage" , "You don't have enough seeds!");
                return false;
            }
            // Calculate reduction
            $workforce_reduction = ($row2['time']) * ($POST['workforce'] * 0.005);
            $base_reduction = $row2['time'] * ($row['efficiency_level'] * 0.01);
            $addTime = $row2['time'] - $workforce_reduction - $base_reduction;
            $date = date("Y-m-d H:i:s");
            $newDate = new DateTime($date);
            $newDate->modify("+{$addTime} seconds");
            try {
                $this->db->conn->beginTransaction();
                
                $this->hungerModel->setHunger('skill');
                $param_crop_type = $POST['crop'];
                $param_crop_quant = 1;
                $param_crop_countdown = date_format($newDate, "Y-m-d H:i:s");
                $param_location = $this->session['location'];
                $sql = "UPDATE farmer SET crop_type=:crop_type,
                        crop_quant=:crop_quant, crop_countdown=:crop_countdown, plot1_harvest=1
                        WHERE location=:location AND username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
                $stmt->bindParam(":crop_quant", $param_crop_quant, PDO::PARAM_INT);
                $stmt->bindParam(":crop_countdown", $param_crop_countdown, PDO::PARAM_STR);
                $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt->execute();
                
                $param_avail_workforce = $row['avail_workforce'] - $POST['workforce'];
                $param_crop_workforce = $POST['workforce'];
                $sql2 = "UPDATE farmer_workforce SET avail_workforce=:avail_workforce,
                                {$this->session['location']}_workforce=:crop_workforce WHERE username=:username";
                $stmt2 = $this->db->conn->prepare($sql2);
                $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":crop_workforce", $param_crop_workforce, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->execute();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory($param_item_seed, -$row2['seed_required'], true);
                // Only gain xp when warrior level is below 30 or if profiency is farmer
                if($this->session['farmer']['level'] < 30 || $this->session['profiency'] == 'farmer') { 
                    $this->response->addTo("levelUP", $this->UpdateGamedata->updateXP('farmer', $row2['experience']));
                }
                
                $this->db->conn->commit();
            }
            catch (Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
            $this->db->closeConn();
            $this->response->addTo("data", $row2['experience'], array("index" => "xpGained"));
            $this->response->addTo("gameMessage", "Planted crops, {$row2['experience']} famer xp gained");
            $this->response->addTo("data", $this->hungerModel->getHunger(), array("index" => "newHunger"));
            $this->response->addTo("data", $param_avail_workforce, array("index" => "availWorkforce"));
        }
    }
?>