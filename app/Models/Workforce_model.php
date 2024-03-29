<?php
class Workers_model extends model
{
    function __construct($session)
    {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @param string $skill
     * @param string $level
     *
     * @return void
     */
    public function upgradeEfficiency(string $skill, string $level)
    {

        $sql = "UPDATE {$skill}_workforce SET efficiency_level=:efficiency_level WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":efficiency_level", $param_efficiency_level, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function upgradeEffiency2($POST)
    {
        // $POST variable holds the post data
        // This function is called from an AJAX request from citycentre.js
        // Function to upgrade efficiency level for farmer and miner
        $skill = $POST['skill'];
        if (!in_array($skill, array('farmer', 'miner'))) {
            return false;
        }

        $param_username = $this->username;
        $sql = "SELECT efficiency_level FROM {$skill}_workforce WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $param_level = $this->session[$skill]['level'];
        $sql = "SELECT max_efficiency_level FROM level_data WHERE level=:level";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
        $stmt->execute();
        $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row2['max_efficiency_level'] == $row['efficiency_level']) {
            $this->response->addTo("errorGameMessage", "You have reached the maximum efficiency level for your skill level");
            return false;
        }

        $cost = $row['efficiency_level'] * 150;

        if ($this->session['gold'] > $cost) {
            $this->response->addTo("errorGameMessage", "You don't have enough gold to upgrade!");
            return false;
        }
        try {
            $this->db->conn->beginTransaction();

            $param_efficiency_level = $row['efficiency_level'] + 1;
            $param_username = $this->username;
            $sql = "UPDATE {$skill}_workforce SET efficiency_level=:efficiency_level WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":efficiency_level", $param_efficiency_level, PDO::PARAM_INT);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();

            // Update inventory
            $this->UpdateGamedata->updateInventory('gold', -$cost, true);

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
            return false;
        }
        $this->response->addTo("gameMessage", "Your efficiency level for {$skill} workforce is now {$param_efficiency_level}");
        $this->response->addTo("data", $param_efficiency_level, array("index" => "effiencyLevel"));
    }
}
