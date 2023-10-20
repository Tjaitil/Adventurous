<?php

namespace App\models;

use App\libs\model;
use \PDO;


class TavernPrices_model extends model
{

    public function __construct()
    {
        parent::__construct('tavern_prices');
    }

    /**
     * Find price for tavern worker
     *
     * @param string $type
     *
     * @return array
     */
    public function find(string $type)
    {
        $param_type = $type;

        $sql = "SELECT price FROM tavern_prices WHERE type=:type";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results !== false ? $results : [];
    }
}
