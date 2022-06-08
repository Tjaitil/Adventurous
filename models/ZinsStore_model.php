<?php
    class ZinsStore_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function tradeItem($POST) {
            if(!in_array($POST['item'], array('daqloon horns', 'daqloon scale'))) {
                $this->response->addTo("errorGameMessage", "Zins is not interested in trading that item");
                return false;
            }
            
            $param_username = $this->username;
            $param_item = strtolower($POST['item']); 
            $sql = "SELECT amount FROM inventory WHERE username=:username AND item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->execute();
            if(!$stmt->rowCount() > 0) {
                $this->response->addTo("errorGameMessage", "You don't have {$POST['item']} in your inventory");
                return false;
            }
            $amount = $stmt->fetch(PDO::FETCH_OBJ)->amount;
            if($amount === 0 || $amount < $POST['amount']) {
                $this->response->addTo("errorGameMessage", "You don't have that many");
                return false;
            }
            $sql = "SELECT store_value FROM items WHERE name=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = $POST['item']; 
            $stmt->execute();
            if(!$stmt->rowCount()) {
                $this->response->addTo("errorGameMessae", "Something unexpected happened, please try again");
                return false;
            }
            $price = $stmt->fetch(PDO::FETCH_OBJ)->store_value;

            try {
                $this->db->conn->beginTransaction();
                
                // Update inventory
                $this->UpdateGamedata->updateInventory('gold', $price * $POST['amount']);
                $this->UpdateGamedata->updateInventory($POST['item'], - $POST['amount'], true);

                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
                return false;
            }
        }
    }
?>