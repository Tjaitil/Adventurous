<?php
class Trader_model extends model
{
    public $username;
    public $session;
    public $assignment_types;
    private $assignment_type;
    private $assignment_amount;
    private $cargo;
    private $favor;

    function __construct($session)
    {
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
        $this->assignment_types = $assignment_types = restore_file('trader_assignment_types', true);
        $this->commonModels(true, false);
    }
    private function getAssignmentTypeData($type)
    {
        $assignment_type = array_values(array_filter($this->assignment_types, function ($key) use ($type) {
            return ($key['type'] == $type);
        }))[0];
        return $assignment_type;
    }
    public function newAssignment($POST)
    {
        // $POST variable holds the post data
        // This function is called from an AJAX request
        // Function to set a new trader assignment
        $echo_data = array();
        $echo_data['gameMessages'] = array();

        $assignment_id = $POST['assignment_id'];

        if (!$this->checkHunger()) return false;
        /*$assignment_amount = str_replace(" ", "+", $assignment_amount);*/
        $param_username = $this->username;
        $sql = "SELECT assignment_id, cart FROM trader WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['assignment_id'] > 0) {
            $this->response->addTo(
                'gameMessage',
                "Finish your assignment before taking a new one",
                array("error" => true)
            );
            return false;
        }
        if ($row['cart'] == 'none') {
            $this->response->addTo(
                'gameMessage',
                "You don't have a cart. Go buy one at a travel bureau!",
                array("error" => true)
            );
            return false;
        }
        $param_assignment_id = $assignment_id;
        $sql = "SELECT destination, assignment_amount, time, assignment_type FROM trader_assignments WHERE assignment_id=:assignment_id";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_STR);
        $stmt->execute();
        $assignment_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $assignment_type_data = $this->getAssignmentTypeData($assignment_data['assignment_type']);

        //countdown
        $add_time = $assignment_data['time'];
        $date = date("Y-m-d H:i:s");
        $new_date = new DateTime($date);
        $new_date->modify("+{$add_time} seconds");
        try {
            $this->db->conn->beginTransaction();

            $param_id = $assignment_id;
            $param_trading_countdown = date_format($new_date, "Y-m-d H:i:s");
            $sql = "UPDATE trader SET assignment_id=:assignment_id, trading_countdown=:trading_countdown WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":assignment_id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":trading_countdown", $param_trading_countdown, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();

            // Only gain xp when warrior level is below 30 or if profiency is trader and assignment_xp is greater than 0
            if ($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader') {
                $echo_data['levelUP'][] = $this->UpdateGamedata->updateXP('trader', $assignment_type_data['xp']);
                $xpUpdate = true;
            }

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->errorHandler->catchAJAX($this->db, $e);
            return false;
        }
        $data = array();
        $sql2 = "SELECT assignment_id, cart, cart_amount, delivered,
                    (SELECT capasity FROM travelbureau_carts WHERE wheel= cart) as capasity FROM trader
                     WHERE username=:username";
        $stmt2 = $this->db->conn->prepare($sql2);
        $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $this->username;
        $stmt2->execute();
        $data['trader_data'] = $stmt2->fetch(PDO::FETCH_ASSOC);

        $sql4 = "SELECT base, destination, cargo, assignment_amount, assignment_type
                         FROM trader_assignments
                         WHERE assignment_id=:assignment_id";
        $stmt4 = $this->db->conn->prepare($sql4);
        $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_INT);
        $param_assignment_id = $assignment_id;
        $stmt4->execute();
        $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $data['trader_data']['base'] = $row4['base'];
        $data['trader_data']['destination'] = $row4['destination'];
        $data['trader_data']['cargo'] = $row4['cargo'];
        $data['trader_data']['assignment_amount'] = $row4['assignment_amount'];
        $data['trader_data']['assignment_type'] = $row4['assignment_type'];

        $this->db->closeConn();
        if (isset($xpUpdate)) {
            $this->response->addTo('gameMessage', "New assignment taken, {$xpUpdate} trader xp gained");
        }
        ob_start();
        get_template('traderAssignment', $data['trader_data'], true);
        $this->response->addTo('html', ob_get_clean());
        $this->response->send();
    }
    public function pickUp()
    {
        //AJAX function
        if (!$this->checkHunger()) return false;
        $echo_data = array();
        $echo_data['gameMessages'] = array();

        $param_username = $this->username;
        $sql = "SELECT t.assignment_id, t.cart, t.cart_amount, t.delivered, ta.assignment_amount, ta.assignment_type, ta.base
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $favor = ($row['assignment_type'] === 'favor') ? true : false;

        if (!$row['assignment_id'] > 0) {
            $this->response->addTo('errorGameMessages', "You don't have any assignment at the moment");
            return false;
        }
        if ($row['base'] != $this->session['location']) {
            $this->response->addTo('errorGameMessage', "You are in the wrong city to pick up items");
            return false;
        }

        $param_wheel = $row['cart'];
        $sql = "SELECT capasity FROM travelbureau_carts WHERE wheel=:wheel";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":wheel", $param_wheel, PDO::PARAM_STR);
        $stmt->execute();
        $row3 = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row['cart_amount'] == $row3['capasity']) {
            $this->response->addTo("ERROR: Your cart is full", true);
            return false;
        }
        if ($row['assignment_amount'] - $row['cart_amount'] <= 0) {
            $this->response->addTo('errorGameMessage', "You don't need to pick up more items");
            return false;
        }
        $cart_space = $row3['capasity'] - $row['cart_amount'];
        // If assignment_amount is less than the cart space available;
        if ($row['assignment_amount'] < $cart_space + $row['delivered']) {
            $cart_space = $row['assignment_amount'] - $row['delivered'];
        }

        try {
            $this->db->conn->beginTransaction();
            $param_cart_amount = $row['cart_amount'] + $cart_space;
            $param_username = $this->username;
            $sql = "UPDATE trader SET cart_amount=:cart_amount WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":cart_amount", $param_cart_amount, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->errorHandler->catchAJAX($this->db, $e);
            return false;
        }
        $this->db->closeConn();
        $this->response->addTo('gameMessages', "You have picked up " . $cart_space . " items");
        $this->response->addTo('data', $param_cart_amount, array("index" => "cartAmount"));
        $this->response->send();
    }
    public function deliver()
    {
        //AJAX function
        $echo_data = array();
        $echo_data['gameMessages'] = array();
        if (!$this->checkHunger()) return false;

        $param_username = $this->username;
        $sql = "SELECT t.assignment_id, t.cart_amount, t.delivered, ta.assignment_amount, ta.cargo, ta.assignment_type,
                            ta.destination, ta.base
                    FROM trader AS t INNER JOIN trader_assignments AS ta ON ta.assignment_id = t.assignment_id
                    WHERE t.username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->favor = ($row['assignment_type'] === 'favor') ? true : false;

        if ($row['assignment_id'] == 0) {
            $this->response->addTo("errorGameMessage", "You don't have a any assignment");
            return false;
        }
        if (!$row['cart_amount'] > 0) {
            $this->response->addTo("errorGameMessage", "You dont't have any goods to deliver");
            return false;
        }
        if ($row['destination'] != $this->session['location']) {
            $this->response->addTo("errorGameMessage", "You are in the wrong city to deliver");
            return false;
        }
        $assignment_type = $row['assignment_type'];
        $assignment_type_data = $this->getAssignmentTypeData($assignment_type);

        $experience = round($assignment_type_data['per_cargo_xp'] * $row['cart_amount']);
        $delivered = $row['delivered'] + $row['cart_amount'];
        $assignment_finished = false;

        if ($row['assignment_amount'] == $delivered) {
            $assignment_finished = true;
            $locations = array("hirtam", "pvitul", "khanz", "ter", "fansalplains");
            if (in_array($row['base'], $locations) || in_array($row['destination'], $locations)) {
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                $diplomacy = $stmt->fetch(PDO::FETCH_ASSOC);

                $param_city = $row['base'];
                $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM city_relations WHERE city=:city";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
                $stmt->execute();
                $city_relations = $stmt->fetch(PDO::FETCH_ASSOC);
                $new_diplomacy = array();
                for ($i = 0; $i < count($diplomacy); $i++) {
                    $new_diplomacy[$locations[$i]] = intval($diplomacy[$locations[$i]]) * $city_relations[$locations[$i]];
                }
            }
        }
        try {
            $this->db->conn->beginTransaction();
            if ($assignment_finished === true) {
                $sql = "UPDATE trader SET assignment_id=0, delivered=0, cart_amount=0
                    WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_username = $this->username;
                $stmt->execute();
                if ($assignment_type === "favor") {
                    $param_Hirtam = $new_diplomacy['hirtam'];
                    $param_Pvitul = $new_diplomacy['pvitul'];
                    $param_Khanz = $new_diplomacy['khanz'];
                    $param_Ter = $new_diplomacy['ter'];
                    $param_FansalPlains = $new_diplomacy['fansalplains'];
                    $param_username = $this->username;
                    $sql2 = "UPDATE diplomacy SET hirtam=:hirtam, pvitul=:pvitul, khanz=:khanz, ter=:ter, fansalplains=:fansalplains
                        WHERE username=:username";
                    $stmt2 = $this->db->conn->prepare($sql2);
                    $stmt2->bindParam(":hirtam", $param_Hirtam, PDO::PARAM_STR);
                    $stmt2->bindParam(":pvitul", $param_Pvitul, PDO::PARAM_STR);
                    $stmt2->bindParam(":khanz", $param_Khanz, PDO::PARAM_STR);
                    $stmt2->bindParam(":ter", $param_Ter, PDO::PARAM_STR);
                    $stmt2->bindParam(":fansalplains", $param_FansalPlains, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt2->execute();
                } else {
                    $reward_amount =  round($row['assignment_amount'] / 12);
                    // If profiency is trader get goods
                    if ($this->session['profiency'] == "trader") {
                        // Update inventory
                        $this->UpdateGamedata->updateInventory($row['cargo'], $reward_amount, true);
                    } else {
                        // Update inventory
                        $this->UpdateGamedata->updateInventory($row['cargo'], $reward_amount, true);
                    }
                }
            } else {
                $param_delivered = $delivered;
                $sql = "UPDATE trader SET delivered=:delivered, cart_amount=0 WHERE username=:username";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":delivered", $param_delivered, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_delivered = $delivered;
                $param_username = $this->username;
                $stmt->execute();
                $param_delivered = $delivered;
            }
            // Only gain xp when warrior level is below 30 or if profiency is trader and assignment_xp is greater than 0
            if ($this->session['trader']['level'] < 30 || $this->session['profiency'] == 'trader') {
                $this->response->addTo("levelUP", $this->UpdateGamedata->updateXP('trader', $experience));
            }

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->errorHandler->catchAJAX($this->db, $e);
            return false;
        }
        $this->db->closeConn();
        //Echo to prevent getting the timestamp from gameMessage()
        if ($row['assignment_amount'] == $delivered) {
            $this->response->addTo("data", true, array("index" => "assignment_finished"));
            $echo_data['assignment_finished'] = true;
            $this->response->addTo("gameMessage", "You finished assignment and received {$reward_amount} of {$row['cargo']}");
            $this->response->addTo("gameMessage", "Diplomacy relations have been updated! See diplomacy tab");

            ob_start();
            get_template('traderAssignment', array("assignment_id" => 0), true);
            $this->response->addTo("html", ob_get_clean());
        } else {
            $this->response->addTo("data", false, array("index" => "assignment_finished"));
            $this->response->addTo("data", $delivered, array("index" => "delivered"));
            $this->response->addTo(
                "gameMessage",
                "You have delivered: {$row['cart_amount']}, Total: {$param_delivered}. Gained
            {$experience} trader experience"
            );
        }
        $this->response->send();
    }
}
