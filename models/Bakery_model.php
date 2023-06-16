<?php

namespace App\models;

use App\libs\model;
use PDO;

class Bakery_model extends model
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all
     *
     * @return array
     */
    public function all()
    {
        $sql = "SELECT b.item_id, b.item, b.price, b.heal, BE.amount, BE.required_item 
        FROM  
        bakery_data AS B INNER JOIN bakery_ingredients AS BE ON b.item_id = be.item_id 
        WHERE b.bakery_item=1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find bakery item
     *
     * @param string $item
     *
     * @return array
     */
    public function find(string $item)
    {
        $sql = "SELECT b.item_id, b.item, b.price, b.heal, BE.amount, BE.required_item 
        FROM  
        bakery_data AS B INNER JOIN bakery_ingredients AS BE ON b.item_id = be.item_id 
        WHERE b.bakery_item=1 AND b.item=:item";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":item", $item, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
