<?php
    class CanvasGameID_model extends model {
    public $username;
    public $session;
    
    function __construct ($session) {
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
    }
    public function checkGameID($POST) {
        // $POST holds the variables from POST object;
        // $live_id is the id from the tab that is accessing the game
        $live_id = $POST['cookieTicket'];
        // cookieNoob is the variable used to determine if the check was on load or if it was after the game has started
        $param_username = $this->username;
        $onLoad_ID = ($POST['cookieNoob'] !== "getOut") ? true : false;
        $sql = "SELECT game_id, session_id FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $db_id = $row['game_id'];
        $session_id = $row['session_id'];
        if($_SESSION['session_id'] !== $session_id && strlen($db_id) > 0) {
            $this->response->addTo("data", false, array("index" => "status"));
            return;
        }
        if(strlen($db_id) <= 0 || ($onLoad_ID == true && isset($_SESSION['gameID'][$live_id]) !== true)) {
            $this->response->addTo("data", "truesession", array("index" => "status"));
            $this->updateGameID($live_id);
            // If it is the first time load, remember that so that when the ID gets
            if($onLoad_ID === true) {
                $_SESSION['gameID'][$live_id] = 1;
            }
            return;
        }
        $db_id = explode("|", $db_id);
        $live_id = explode("|", $live_id);
        if(intval($db_id[1] - 5) === intval($live_id[1]) - 5) {
            $this->response->addTo("data", true, array("index" => "status"));
        }
        else {
            $this->response->addTo("data", false, array("index" => "status"));
        }
    }
    private function updateGameID($live_id) {
        $param_game_id = $live_id;
        $param_username = $this->username;
        $sql = "UPDATE user_data SET game_id=:game_id WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":game_id", $param_game_id, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function updateSessionID() {
        $param_session_id = $_SESSION['session_id'];
        $param_username = $this->username;
        $sql = "UPDATE user_data SET session_id=:session_id WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":session_id", $param_session_id, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function checkSessionID() {
        $param_username = $this->username;
        $sql = "SELECT session_id FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $session_id = $stmt->fetch(PDO::FETCH_OBJ)->session_id;
        if($_SESSION['session_id'] !== $session_id) {
            $_SESSION['outdatedSessionID'] = true;
            // header("Location: /logout");
            exit();
        }
    }
}
