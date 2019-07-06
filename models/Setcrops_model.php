<?php
    class Setcrops_model extends model {
        // Store session username in variable
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
        
        public function getCropTypeData ($crop_type) {
            $data = array();
            $sql = "SELECT farmer_level, time, seed_required FROM crops_data
                    WHERE crop_type=:crop_type AND farmer_level<=:farmer_level AND location=:location";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":crop_type", $param_crop_type, PDO::PARAM_STR);
            $stmt->bindParam(":farmer_level", $param_farmer_level, PDO::PARAM_STR);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $param_crop_type = $crop_type;
            $param_farmer_level = $this->session['farmer']['level'];
            $param_location = $this->session['location'];
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->gameMessage("ERROR: You are in the wrong city to grow this crop!");
                return false;
            }
            $data['crop'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $crop_type . ' seed';
            $param_username = $this->username;
            $stmt->execute();
            $data['inventory_seed'] = $stmt->fetch(PDO::FETCH_ASSOC);
            
            unset($stmt);
            if($data['inventory_seed'] < $data['crop']['seed_required']) {
                $this->gameMessage("ERROR: You don't have enough seeds!");
                return false;
            }
            return $data;
        }
        
        public function setCropData($crop_data) {
                $experience = $crop_data['experience'] + $this->session['farmer']['xp'];
                $workforce = $this->session['location'] . '_workforce';
                try {
                    $this->conn->beginTransaction();
                    
                    $sql = "UPDATE Farmer SET fields_avail=:fields_avail, grow_type=:grow_type,
                            grow_quant=:grow_quant, grow_countdown=:grow_countdown, plot1_harvest='true'
                            WHERE username=:username";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam(":fields_avail", $param_fields_avail, PDO::PARAM_STR);
                    $stmt->bindParam(":grow_type", $param_grow_type, PDO::PARAM_STR);
                    $stmt->bindParam(":grow_quant", $param_grow_quant, PDO::PARAM_STR);
                    $stmt->bindParam(":grow_countdown", $param_grow_countdown, PDO::PARAM_STR);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_fields_avail = $crop_data['fields_available'];
                    $param_grow_type = $crop_data['type'];
                    $param_grow_quant = $crop_data['quantity'];
                    $param_grow_countdown = $crop_data['countdown'];
                    $param_username = $this->username;
                    $stmt->execute();
                    
                    $sql2 = "UPDATE farmer_workforce SET avail_workforce=:avail_workforce, $workforce=:crop_workforce
                             WHERE username=:username";
                    $stmt2 = $this->conn->prepare($sql2);
                    $stmt2->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
                    $stmt2->bindParam(":crop_workforce", $param_crop_workforce, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $param_avail_workforce = $crop_data['new_workforce'];
                    $param_crop_workforce = $crop_data['workforce_quant'];
                    $param_username = $this->username;
                    $stmt2->execute();
                    
                    update_xp($this->conn, $this->username, 'farmer', $experience);
                    update_inventory($this->conn, $this->username, $crop_data['type'] . ' seed', -$crop_data['seed_required'], true);
                    
                    $this->conn->commit();
                }
                catch (Exception $e) {
                    $this->conn->rollBack();
                    new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                    $this->gameMessage("ERROR: Something unexpected happened, please try again");
                    return false;
                }
                $this->closeConn();
                $_SESSION['gamedata']['farmer']['xp'] = $experience;
            }
        }
?>