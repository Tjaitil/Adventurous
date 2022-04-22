<?php
    class Repair_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function checkItemRepairability($POST) {
            $item = $POST;
            if(strpos($item, 'broken') === false) {
                $_SESSION['conversation']['conv_index'] = "kpsrQrr1";
                return array(true, false);
            }
            $repaired_item = trim(str_replace("broken", " ", $item));
            $sql = "SELECT mineral_required, cost FROM armory_items_data WHERE item=:item";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_item = strtolower($repaired_item);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($this->session['profiency'] === 'miner') {
                $row['cost'] *= 0.50;
            }
            else {
                $row['cost'] *= 0.90;
            }
            $_SESSION['conversation']['conv_index'] = "kpsrQrq";
            $active_conversation = $_SESSION['conversation']['active_dialogues'] =
                    array("a|" . $item . " can be repaired for " . $row['cost']  . " gold|r|selectItemConv.removeEvent");
            $_SESSION['conversation']['information']['item'] = $item;
            $_SESSION['conversation']['information']['cost'] = $row['cost'];
            return array(true, $active_conversation);
        }
        public function repairItem($POST) {
            // $POST variable holds the post data
            // This function is called from conversation.js, kapys
            $item = $POST['item'];            
            $repaired_item = trim(str_replace("broken", " ", $item));
            
            if($POST['cost'] > $this->session['gold']) {
                $_SESSION['conversation']['conv_index'] = "kpsrQrq1";
                return array(true, false);
            }
            try {
                $this->db->conn->beginTransaction();
                
                // Remove the broken item and replace it with a new item
                $this->UpdateGamedata->updateInventory($item, -1);
                $this->UpdateGamedata->updateInventory($repaired_item, 1);
                
                $this->UpdateGamedata->updateInventory('gold', - $POST['cost'], true);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                $_SESSION['conversation']['conv_index'] = "kpsrQrq2";
                return array(true, false);  
            }
            $_SESSION['conversation']['conv_index'] = "kpsrQrqrr1";
            return array(true, false);
        }
    }
?>