<?php
    class Artefact_model extends model {
        public $username;
        public $session;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
    
        public function updateArtefact() {
    
            $charges = preg_match('#\((.*)\)#', $x, $y)[0];
            
            try {
                $this->db->conn->beginTransaction();
                
                if($charges > 0) {
                    update_inventory($this->db->conn, $this->username, x, 1);
                }
                
                update_inventory($this->db->conn, $this->username, x, -1);
                
                $this->db->conn->commit();
            }
            catch(Exception $e) {
                $this->db->conn->rollBack();
                $this->reportError($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
        }
    }
?>