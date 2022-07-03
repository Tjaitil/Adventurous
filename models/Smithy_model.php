<?php
class Smithy_model extends model {
    public $username;
    public $session;

    function __construct($session) {
        parent::__construct();
        $this->username = $session['username'];
        $this->session = $session;
        $this->commonModels(true, false);
    }
    public function getData($js = false) {
        $data = array();
        $sql = "SELECT a.item_id, a.item, a.price, b.item_required, b.required_amount, 
                    (SELECT miner_level FROM minerals_data WHERE mineral_type=a.mineral) as level 
                FROM smithy_items AS a 
                INNER JOIN smithy_items_required AS b ON a.item_id=b.item_id 
                ORDER BY level ASC";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data = array();


        $data = $this->createItemStoreData($rows, "iron");
        // $data['iron'] = $this->createItemStoreData($rows, "iron");
        // $data['steel'] = $this->createItemStoreData($rows, "steel");
        // $data['gargonite'] = $this->createItemStoreData($rows, "gargonite");
        // $data['adron'] = $this->createItemStoreData($rows, "adron");
        // $data['yeqdon'] = $this->createItemStoreData($rows, "yeqdon");

        if ($js === false) {
            return $data;
        } else {
            $this->response->addTo("data", $data, array("index" => "data"));
        }
    }
    public function createItemStoreData($rows)
    {
        $data = array();

        // $rows = array_filter($rows, function($element) use ($mineral) {
        //     return strpos($element['item'], $mineral) !== false;
        // });

        $setAmountItems = array("arrow");
        foreach ($rows as $key => $value) {
            $index = array_search($value['item_id'], array_column($data, 'item_id'));
            if ($index !== false) {
                $data[$index]['required'][] =
                    array("required_amount" => $value['required_amount'], "required_item" => $value['item_required']);
            } else {
                $value['required'][] = array("required_amount" => $value['required_amount'], "required_item" => $value['item_required']);
                (strpos($value['item'], $setAmountItems[0]) !== false) ? $value['setAmount'] = 5 : "";
                array_push($data, $value);
            }
        }
        return $data;
    }
    public function smith($POST) {
        // $POST variable holds the post data
        // Smith items from mineral bar and ores
        $item = strtolower($POST['item']);
        $amount = $POST['amount'];
        

        $param_item = $item;
        $sql = "SELECT a.item_id, a.item, a.price, b.item_required, b.required_amount, 
                    (SELECT miner_level FROM minerals_data WHERE mineral_type=a.mineral) as level 
                FROM smithy_items AS a 
                INNER JOIN smithy_items_required AS b ON a.item_id=b.item_id 
                WHERE a.item=:item
                ORDER BY level ASC";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->execute();
        if (!$stmt->rowCount() > 0) {
            $this->response->addTo("errorGameMessage", "You cannot craft this item!");
            return false;
        }
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $have_required_items = true;
        $check_item = 'none';
        foreach ($row as $key) {
            $check_item = get_item($this->session['inventory'], $key['item_required']);
            if (is_null($check_item) || $check_item['amount'] < $key['required_amount']) {
                $have_required_items = false;
                $check_item = $key['item_required'];
                break;
            }
        }
        if ($have_required_items != true) {
            $this->response->addTo("errorGameMessage", "You don't have enough of $check_item");
        }

        $cost = $row[0]['price'] * $amount;
        // If profiency is farmer pay 20 % less
        if ($this->session['profiency'] === 'miner') {
            $cost *= 0.80;
        }

        if ($this->session['gold'] < $cost) {
            $this->response->addTo("errorGameMessage", "You don't have enough gold");
            return false;
        }

        // Check if level requirement is set or not
        if ($row[0]['level'] > $this->session['miner']['level']) {
            $this->response->addTo("errorGameMessage", "Your level is too low");
            return false;
        }

        try {
            $this->db->conn->beginTransaction();
            if (strpos($item, "arrows") !== false || strpos($item, "knives") !== false) {
                $this->UpdateGamedata->updateInventory($item, $amount * 5);
            } else {
                // Subtract gold from inventory
                $this->UpdateGamedata->updateInventory($item, $amount);
                foreach ($row as $key) {
                    $this->UpdateGamedata->updateInventory($key['item_required'], -$key['required_amount'] * $amount);
                }
            }
            $this->UpdateGamedata->updateInventory("gold", -$cost);

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
            return false;
        }
        $this->db->closeConn();
    }
}
