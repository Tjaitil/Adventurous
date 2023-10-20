<?php
class CityCentre_model extends model
{
    public $username;
    public $session;

    function __construct($session)
    {
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
    }
    public function getData()
    {
        $data = array();

        $param_username = $this->username;
        $sql = "SELECT artefact FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $data['artefact_data'] = $stmt->fetch(PDO::FETCH_ASSOC);

        $sql = "SELECT permits, location FROM miner WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        //$param_username already defined in statement 1
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $data['permits'][$row['location']] = $row['permits'];
        }

        $sql = "SELECT frajrite_items, wujkin_items FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $this->username;
        $stmt->execute();
        $data['unlock_items'] = $stmt->fetch(PDO::FETCH_ASSOC);

        if (in_array($this->session['location'], array("fansal-plains", "hirtam", "khanz", "pvitul", "ter"))) {
            $sql = "SELECT " . $this->session['location'] . " FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            //$param_username already defined in statement 1
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_NUM);
            $data['diplomacy'] = $row[0];
        }

        $sql = "SELECT fw.efficiency_level as farmer, mw.efficiency_level as miner
                    FROM miner_workforce as mw INNER JOIN farmer_workforce as fw ON fw.username=mw.username
                    WHERE mw.username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $this->username;
        $stmt->execute();
        $data['effiency'] = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->db->closeConn();
        return $data;
    }
}
