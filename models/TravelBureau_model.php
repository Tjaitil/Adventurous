<?php
class TravelBureau_model extends model
{
    public $username;
    public $session;

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all cartss
     *
     * @return array
     */
    public function all()
    {
        return [];
    }

    /**
     * Find cart
     *
     * @param string $item
     *
     * @return array
     */
    public function find(string $item)
    {
        $param_item = $item;

        $sql = "SELECT a.item, a.store_value, a.capasity, b.item_id, b.required_item, b.amount 
                FROM travelbureau_carts AS a
                INNER JOIN travelbureau_carts_req_items AS b ON a.item=b.item_id
                WHERE a.item=:item";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update(string $cart)
    {
        $param_username = $this->username;
        $param_type = $cart;


        $sql = "UPDATE trader SET cart=:cart WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":cart", $param_type, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);

        $param_username = $this->username;
        $stmt->execute();
    }

    public function getData($js = false)
    {
        /*$city = $_SESSION['gamedata']['location'];
            /*$sql = "SELECT type, $city, value FROM travelbureau_horses";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_amount = 0;
            $stmt->execute();
            $data = array();
            $data['horse_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $data['city'] = $city;*/

        $param_username = $this->username;
        $sql = "SELECT cart FROM trader WHERE username=:username";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $data['cart'] = $stmt->fetch(PDO::FETCH_OBJ)->cart;

        $sql = "SELECT type, wheel, wood, price, capasity, mineral_amount, wood_amount FROM travelbureau_carts";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $data['cart_shop'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->db->closeConn();
        if ($js === true) {
            ob_start();
            get_template('cartShop', $data, true);
            $this->response->addTo("html", ob_get_clean());
        } else {
            return $data;
        }
    }
    public function buyItem($POST)
    {
        $item = strtolower($POST['item']);
        $cities = array("towhar", "golbak", "snerpiir", "krasnur", "tasnobil", "cruendo", "fagna");
        if (array_search($this->session['location'], $cities) === false) {
            $this->response->addTo("errorGameMessage", "You are in the wrong city");
            return false;
        }

        $param_type = $item;
        $sql = "SELECT type, wheel, wood, price, mineral_amount, wood_amount 
                    FROM travelbureau_carts WHERE type=:type";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$stmt->rowCount() > 0) {
            $this->response->addTo("errorGameMessage", "The item you are trying to buy does not exists!");
            return false;
        }

        $mineral = $row['wheel'] . ' bar';
        $mineral_amount = get_item($this->session['inventory'], $mineral);
        if (is_null($mineral_amount) || $mineral_amount['amount'] < $row['mineral_amount']) {
            $this->response->addTo("errorGameMessage", "You don't have enough {$mineral}s to buy this!");
            return false;
        }
        $wood = $row['wood'] . ' logs';
        $wood_amount = get_item($this->session['inventory'], $wood);
        if (is_null($wood_amount) || $wood_amount['amount'] < $row['wood_amount']) {
            $this->response->addTo("errorGameMessage", "You don't have enough {$wood}s to buy this!");
            return false;
        }

        try {
            $this->db->conn->beginTransaction();

            $param_username = $this->session['username'];
            //Update cart to trader/ horse to user
            $sql = "UPDATE trader SET cart=:cart WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":cart", $param_type, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_username = $this->username;
            $stmt->execute();

            //Update gold amount, if $row_count is greater than 0 the user has either cart/horse which is then sold
            $this->UpdateGamedata->updateInventory('gold', -$row['price']);
            $this->UpdateGamedata->updateInventory($mineral, -$row['mineral_amount']);
            $this->UpdateGamedata->updateInventory($wood, -$row['wood_amount'], true);

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
            return false;
        }
        $this->db->closeConn();
        $this->response->addTo("gameMessage", "You bought a {$item} horse for {$row['price']} gold");
        $this->response->addTo("data", $item, array("index" => "cart"));
    }
}
