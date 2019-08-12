<?php
    class Artefact_model extends model {
        public $username;
        public $session;
        
        function __construct ($username, $session) {
            parent::__construct();
            $this->username = $username;
            $this->session = $session;
        }
    
        public function updateArtefact() {
    
            $charges = preg_match('#\((.*)\)#', $x, $y)[0];
            
            try {
                $this->conn->beginTransaction();
                
                if($charges > 0) {
                    update_inventory($this->conn, $this->username, x, 1);
                }
                
                update_inventory($this->conn, $this->username, x, -1);
                
                $this->conn->commit();
            }
            catch(Exception $e) {
                $this->conn->rollBack();
                new ajaxexception($e->getFile(), $e->getLine(), $e->getMessage());
                $this->gameMessage("ERROR: Something unexpected happened, please try again", true);
                return false;
            }
        }
    }
?>