<?php

namespace App\models;

use App\libs\model;
use \DateTime;
use \PDO;

class TraderAssignment_model extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function all()
    {
        $sql = "SELECT base, destination, cargo, assignment_amount, assignment_type, time,
        FROM trader_assignments";

        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find trader assignment by id
     *
     * @param int $id assignment id
     *
     * @return array[]
     */
    public function find(int $id)
    {
        $param_assignment_id = $id;

        $sql = "SELECT base, destination, cargo, assignment_amount, assignment_type, time
        FROM trader_assignments
        WHERE assignment_id=:assignment_id";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_INT);

        $stmt->execute();

        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results === false ? [] : $results;
    }

    /**
     * Get countdown for new trader assignments
     *
     * @return int
     */
    public function getTraderAssigmentCountdown()
    {
        $sql = "SELECT date_inserted FROM trader_assignments ORDER BY date_inserted DESC LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $date = date_timestamp_get(new DateTime($row['date_inserted']));
        return $date;
    }

    public function findUserAssignment()
    {
    }
}
