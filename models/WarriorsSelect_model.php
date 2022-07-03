<?php
class WarriorsSelect_model extends model
{
    public $username;
    public $session;

    function __construct($session)
    {
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
    }
    public function getAvailableWarriors($js = false)
    {
        $param_username = $this->username;
        $sql = "SELECT DISTINCT 
            a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.left_hand, 
            a.body, a.right_hand, a.legs, a.boots, b.type, 
            c.stamina_level, c.technique_level, 
                c.precision_level, c.strength_level,
                (SELECT SUM(attack) 
                 FROM armory_items_data 
                 WHERE item 
                 IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.legs, a.boots)) 
                 AS attack, 
                 (SELECT SUM(defence) 
                 FROM armory_items_data 
                 WHERE item 
                 IN (a.helm, a.ammunition, a.left_hand, a.body, a.right_hand, a.legs, a.boots)) 
                 AS defence 
            FROM warrior_armory as a 
            LEFT JOIN warriors as b
            ON a.warrior_id = b.warrior_id and a.username = b.username 
            JOIN warriors_levels as c
            ON a.warrior_id = c.warrior_id and b.username = c.username 
            WHERE a.username=:username
                AND b.fetch_report=0 AND b.training_type='none'
                AND b.mission=0";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->closeConn();
        if ($js === false) {
            return $row;
        } else {
            ob_start();
            get_template('warrior_select', $row, false);
            $this->response->addTo("html", ob_get_clean(), array("index" => "warriors"));
        }
    }
}
