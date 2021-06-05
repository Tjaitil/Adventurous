<?php
    class Loot_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
            $this->commonModels(true, false);
        }
        public function addLoot($POST) {
            try {
                $this->db->conn->beginTransaction();
                $this->UpdateGamedata->updateInventory($POST['item'], 1, true);
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->errorHandler->catchAJAX($this->db, $e);
                return false;
            }
            $this->db->closeConn();
        }
    }
?>