<?php

namespace App\models;

use App\libs\model;
use PDO;

class TavernTimes_model extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function find(string $location)
    {
        $param_username = $this->session;

        $sql = "SELECT new_workers, {$location} FROM tavern_times WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results !== false ? $results : [];
    }

    public function update(string $location)
    {
        $sql = "UPDATE tavern_times SET new_workers=:new_workers, {$location}=1 WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":new_workers", $param_new_workers, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function reset()
    {
        $sql = "UPDATE tavern_times 
                SET towhar=0, krasnur=0, snerpiir=0, golbak=0, tasnobil=0, cruendo=0 
                WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $this->username;
        $stmt->execute();
    }
}
