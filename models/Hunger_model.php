<?php

namespace App\models;

use App\libs\model;
use \PDO;

class Hunger_model extends model
{
    protected $hungerError = "Your hunger is too high, decrease hunger first";

    function __construct()
    {
        parent::__construct(true);
    }

    /**
     * Update hunger
     *
     * @return array
     */
    public function update(int $hunger)
    {
        $new_hunger = $hunger;
        $this->username;

        $sql = "UPDATE user_data SET hunger=:hunger WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":hunger", $new_hunger, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
    }

    /**
     * Get hunger data
     *
     * @return array
     */
    public function get()
    {
        $param_username = $this->username;
        $sql = "SELECT hunger, hunger_date FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getHungerError()
    {
        return $this->hungerError;
    }
    public function checkHunger()
    {
        if (intval($this->session['hunger']) < 15) {
            return true;
        } else {
            return false;
        }
    }
    public function getHunger($updated_message = false)
    {
        return $_SESSION['gamedata']['hunger'];
    }

    /**
     * Set new hunger in db and session based on method taken
     *
     * @param string $action Action that player is doing
     * @return void
     */
    public function setHunger($action)
    {
        if ($action === 'skill') {
            $hunger_cost = 15;
        }
        $new_hunger = $_SESSION['gamedata']['hunger'] - $hunger_cost;
        if ($new_hunger < 0) {
            $new_hunger = 0;
        }
        $param_username = $this->session['username'];
        $sql = "UPDATE user_data SET hunger=:hunger WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":hunger", $new_hunger, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $_SESSION['gamedata']['hunger'] = $new_hunger;
    }
    public function updateHunger($POST)
    {
        $seconds = $POST['time'];
        // If intval of seconds is 0 there has been tampering of the date object
        if (intval($seconds) === 0) {
            $seconds = 2400;
        }
        if (!isset($_SESSION['gamedata']['hunger'])) {
            $hunger = 0;
        } else {
            $hunger = $_SESSION['gamedata']['hunger'];
        }

        $minutes = round($seconds / 60);
        // For every minute reduce hunger by 2.5 points;
        $new_hunger =  $hunger - ($minutes * 2.5);
        if ($new_hunger < 0) {
            $new_hunger = 0;
        }
        $this->response->addTo("data", $new_hunger, array("index" => "newHunger"));
    }
    public function eat($POST)
    {
        // $POST variable holds post data
        // This function is called from an ajax request
        // Function to replenish players hunger bar by eating food
        $item = strtolower($POST['item']);
        $amount = $POST['amount'];
        $item_data = get_item($this->session['inventory'], $item);

        if ($_SESSION['gamedata']['hunger'] === 100) {
            $this->response->addTo("errorGameMessage", "Your hunger doesn't need to be replenished");
            return false;
        }

        if (!count($item_data) > 0) {
            $this->response->addTo("errorGameMessage", "You don't currently have that item in your inventory");
            return false;
        }
        if (!$item_data['amount'] > 0) {
            $this->response->addTo("errorGameMessage", "You don't currently have that many in your inventory");
            return false;
        }

        $param_item = $item;
        $sql = "SELECT heal FROM bakery_data WHERE item=:item";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->execute();
        if (!$stmt->rowCount() > 0) {
            $this->response->addTo("errorGameMessage", "This item wouldn't taste good!");
            return false;
        }
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $param_username = $this->username;
        $sql = "SELECT hunger, hunger_date FROM user_data WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row2 = $stmt->fetch(PDO::FETCH_ASSOC);

        $hunger = ($row['heal'] * $amount) + $row2['hunger'];
        if ($hunger > 100) $hunger = 100;
        try {
            $this->db->conn->beginTransaction();

            $param_hunger = $hunger;
            $param_hunger_date = date("Y-m-d H:i:s");
            $param_username = $this->username;
            $sql = "UPDATE user_data SET hunger=:hunger, hunger_date=:hunger_date WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":hunger", $param_hunger, PDO::PARAM_INT);
            $stmt->bindParam(":hunger_date", $param_hunger_date, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();

            // Update inventory
            $this->UpdateGamedata->updateInventory($item, -$amount, true);

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
            return false;
        }
        $_SESSION['gamedata']['hunger'] = $param_hunger;
        $this->response->addTo("data", $param_hunger, array("index" => "newHunger"));
    }
}
