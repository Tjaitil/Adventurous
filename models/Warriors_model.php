<?php
class Warriors_model extends model
{
    public $username;
    public $session;

    private $_data;

    /**
     * Construct
     *
     * @param array $session Session array
     * @param null|Database $db Provided DB if its loaded by another model or 
     */
    function __construct($session, Database $db = null)
    {
        if (!is_object($db)) {
            parent::__construct(true);
            $this->db = $this->db;
        } else {
            $this->db = $db;
        }
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
    }

    /**
     * Get all available warriors
     * 
     * @param bool $js Called by ajax or not
     * @return html|array Html template of warriors or array containing data
     */
    public function getAvailableWarriors($js = false)
    {
        $param_username = $this->username;
        $sql = "SELECT DISTINCT 
            a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.left_hand, 
            a.body, a.right_hand, a.legs, a.boots, b.type, 
            c.stamina_level, c.stamina_xp, c.technique_level, c.technique_xp, 
            c.precision_level, c.precision_xp, c.strength_level, c.strength_xp,
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
            AND b.army_mission=0";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $this->_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($js === false) {
            return $this->_data;
        } else {
            $this->outputHtmlTemplate();
        }
    }

    /**
     * Get specified available warriors
     * 
     * @param bool $js Called by ajax or not
     * @param int[] $selected An array containing specified warrior ids
     * @param bool $available Bool to get only available warriors 
     * @return html|array Html template of warriors or array containing data
     */
    public function getSelectedWarriors($js = false, $selected = [], $available = false)
    {
        $query_array = $selected;
        $in  = str_repeat('?,', count($query_array) - 1) . '?';
        $query_array[] = $this->username;

        $sql = "SELECT DISTINCT 
        a.warrior_id, a.helm, a.ammunition, a.ammunition_amount, a.left_hand, 
        a.body, a.right_hand, a.legs, a.boots, b.type, 
        c.stamina_level, c.stamina_xp, c.technique_level, c.technique_xp, 
        c.precision_level, c.precision_xp, c.strength_level, c.strength_xp,
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
        WHERE a.warrior_id IN ($in) AND a.username=?";
        if($available === true) {
            $sql .= "AND b.fetch_report=0 AND b.training_type='none'
            AND b.army_mission=0";
        }
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($query_array);
        $this->_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($js === false) {
            return $this->_data;
        } else {
            $this->outputHtmlTemplate();
        }
    }

    /**
     * Updated specified Warriors
     * 
     * @param array $statements Array containing sql set statements with data
     * $statements = [
     *      'statement' => [
     *          'value'      => (boolean) value to insert into DB
     *          'table_name' => (string) DB table name \n
     *      ]
     * ]
     * @param Int[] $selected An array containing specified warrior ids
     * @return mixed
     */
    public function updateSelectedWarriors($statements, $selected = [])
    {
        $query_array = $selected;
        $in  = str_repeat('?,', count($selected) - 1) . '?';

        $sql = "UPDATE warriors SET ";
        foreach ($statements as $key => $value) {
            $sql .= "{$value['table_name']}=?";
            array_unshift($query_array, $value['value']);
        }

        array_push($query_array, $this->username);
        $sql .= " WHERE warrior_id IN ($in) AND username=?";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute($query_array);
    }

    /**
     * Update specified warriors skills xp
     *
     * @param array $warrior_data Array of data for each warrior
     * @return void
     */
    public function updateSelectedWarriorsXP($warrior_data) {

        foreach ($warrior_data as $key) {
            $warrior_melee_check = $key['type'] === "melee";

            $sql = "UPDATE warriors_levels SET stamina_xp=:stamina_xp, technique_xp=:technique_xp,";
            if($warrior_melee_check) {
                $sql .= " strength_xp=:strength_xp ";
            } else {
                $sql .= " precision_xp=:precision_xp ";
            }
            $sql .= "WHERE warrior_id=:warrior_id AND username=:username";

            $param_stamina_xp = $key['stamina_xp'];
            $param_technique_xp = $key['technique_xp'];
            $param_strength_xp = $key['strength_xp'];
            $param_precision_xp = $key['precision_xp'];
            $param_warrior_id = $key['warrior_id'];
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":stamina_xp", $param_stamina_xp, PDO::PARAM_INT);
            $stmt->bindParam(":technique_xp", $param_technique_xp, PDO::PARAM_INT);

            // Determine which type of warrior
            if($warrior_melee_check) {
                $stmt->bindParam(":strength_xp", $param_strength_xp, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(":precision_xp", $param_precision_xp, PDO::PARAM_INT);
            }

            $stmt->bindParam(":warrior_id", $param_warrior_id, PDO::PARAM_INT);
            $stmt->bindParam(":username", $this->username, PDO::PARAM_STR);
            $stmt->execute();
        }
    }

    /**
     * Output warrior template
     *
     * @return html Template
     */
    public function outputHtmlTemplate()
    {
        ob_start();
        get_template('warrior_select', $this->_data, true);
        $this->response->addTo("html", ob_get_clean(), array("index" => "warriors"));
    }

    /**
     * Get warriors from the specified Army Mission
     *
     * @param int $mission_id
     * @return array Array of warrior id's
     */
    public function getArmyMissionWarriors($mission_id) {
        $sql = "SELECT warrior_id FROM warriors WHERE army_mission=:mission_id";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":mission_id", $mission_id, PDO::PARAM_INT);
        $stmt->execute();
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'warrior_id');
    }
}