<?php

namespace App\models;

use App\libs\model;
use PDO;

class UserData_model extends model
{
    public function __construct()
    {
        parent::__construct('user_data');
    }

    /**
     *
     * @return array
     */
    public function find()
    {
        $param_username = $this->username;

        $sql = "SELECT location, map_location, profiency FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();

        $results = $this->fetched_rows = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results !== false ? $results : [];
    }

    public function update()
    {
        $param_username = $this->username;
        $sql = "UPDATE user_data SET artefact WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":artefact", $param_artefact, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }
}
