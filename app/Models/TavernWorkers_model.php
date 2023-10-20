<?php

namespace App\Models;

use App\libs\model;
use \PDO;

class TavernWorkers_model extends model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function all(string $location)
    {
        $param_city = $location;
        $param_username = $this->username;


        $sql = "SELECT type, level FROM tavern_workers WHERE city=:city AND username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results !== false ? $results : [];
    }

    /**
     * Find worker
     *
     * @param string $city Current location for user
     * @param int $level Worker level
     * @param string $type Worker type
     *
     * @return array
     */
    public function find(string $city, int $level, string $type)
    {
        $param_username = $this->username;
        $param_city = $city;
        $param_level = $level;
        $param_type = $type;

        $sql = "SELECT type, level 
                FROM tavern_workers 
                WHERE city=:city AND username=:username AND level=:level AND type=:type";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
        $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetch(PDO::FETCH_ASSOC);

        return $results !== false ? $results : [];
    }

    /**
     * Delete worker
     *
     * @param string $city Current location for user
     * @param int $level Worker level
     * @param string $type Worker type
     *
     * @return void
     */
    public function destroy(string $city, int $level, string $type)
    {
        $param_username = $this->username;
        $param_city = $city;
        $param_level = $level;
        $param_type = $type;

        $sql = "DELETE FROM tavern_workers
                WHERE username=:username AND city=:city AND type=:type AND level=:level LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Delete all workers
     *
     *
     * @return void
     */
    public function destroyAll()
    {
        $param_username = $this->username;

        $sql = "DELETE FROM tavern_workers WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function create(string $location, string $level, string $type)
    {
        $param_username = $this->username;
        $param_city = $location;
        $param_type = $level;
        $param_level = $type;

        $sql = "INSERT INTO tavern_workers (username, city, type, level)
                 VALUES(:username, :city, :type, :level);";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->bindParam(":level", $param_level, PDO::PARAM_INT);
        $stmt->execute();
    }
}
