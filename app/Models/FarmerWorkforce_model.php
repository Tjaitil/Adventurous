<?php

namespace App\Models;

use App\libs\model;
use App\Http\Resources\WorkforceResource;
use \PDO;

class FarmerWorkforce_model extends model
{

    public function all()
    {
        $param_username = $this->username;

        $sql = "SELECT workforce_total, avail_workforce, efficiency_level,
                towhar_workforce, krasnur_workforce
                FROM farmer_workforce WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Undocumented function
     *
     * @param string|null $location
     *
     * @return array $data = [
     *      'workforce_total' => (int) Workforce total,
     *      'avail_workforce' => (int) Available workforce,
     *      'efficiency_level' => (int) Efficiency level,
     * ]
     */
    public function find(string $location)
    {
        $param_username = $this->username;
        $location_column = $location . '_workforce';

        $sql = "SELECT workforce_total, avail_workforce, efficiency_level, $location_column 
                FROM farmer_workforce 
                WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(WorkforceResource $workforceResource)
    {
        $location_column = $workforceResource->location . '_workforce';
        $param_avail_workforce = $workforceResource->avail_amount;
        $param_location_workforce = $workforceResource->location_amount;

        $param_username = $this->username;

        $sql = "UPDATE farmer_workforce 
                SET avail_workforce=:avail_workforce, $location_column=:location_workforce
                WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
        $stmt->bindParam(":location_workforce", $param_location_workforce, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function updateWorkforce(WorkforceResource $workforceResource)
    {
        $param_workforce_total = $workforceResource->total_amount;
        $param_avail_workforce = $workforceResource->avail_amount;
        $param_username = $this->username;

        $sql = "UPDATE farmer_workforce SET workforce_total=:workforce_total, avail_workforce=:avail_workforce
                    WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":workforce_total", $param_workforce_total, PDO::PARAM_STR);
        $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function upgradeEfficiency(int $level)
    {
        $param_efficiency_level = $level;

        $sql = "UPDATE farmer_workforce SET efficiency_level=:efficiency_level WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":efficiency_level", $param_efficiency_level, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

        $stmt->execute();
    }

    public function increaseWorkforce(WorkforceResource $resource)
    {

        $param_username = $this->username;
        $param_workforce_total = $resource->total_amount;
        $param_avail_workforce = $resource->avail_amount;

        $sql = "UPDATE farmer_workforce SET workforce_total=:workforce_total, avail_workforce=:avail_workforce
                WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":workforce_total", $param_workforce_total, PDO::PARAM_INT);
        $stmt->bindParam(":avail_workforce", $param_avail_workforce, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

        $stmt->execute();
    }
}
