<?php

namespace App\models;

use App\libs\model;
use DateTime;
use Exception;
use PDO;

/**
 * @deprecated
 */
class Merchant_model extends model
{
    private $locations = array(
        "fagna", "towhar", "golbak",  "krasnur", "tasnobil", "cruendo", "snerpiir",
        "hirtam", "fansal-plains", "pvitul", "khanz", "ter",
    );

    function __construct()
    {
        parent::__construct();
    }

    public function all(string $location)
    {

        $param_location = $location;

        $sql = "SELECT item, store_value, sell_value, amount 
        FROM merchant_offers WHERE location=:location";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(string $item, string $location)
    {

        $param_item = $item;
        $param_location = $location;

        $sql = "SELECT item, store_value, sell_value, amount 
        FROM merchant_offers WHERE location=:location AND item=:item";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update(string $location, string $item, int $price, int $amount)
    {

        $param_location = $location;
        $param_price = $price;
        $param_amount = $amount;
        $param_item = $item;

        $sql = "UPDATE merchant_offers SET store_value=:user_buy_price, amount=:amount
        WHERE location=:location AND item=:item";
        $stmt = $this->db->conn->prepare($sql);

        $stmt->bindParam(":user_buy_price", $param_price, PDO::PARAM_INT);
        $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
        $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getData($js = false)
    {
        $data = array();
        $data['city'] = $this->session['location'];

        $param_username = $this->username;
        $sql2 = "SELECT assignment_id, cart, cart_amount, delivered,
                    (SELECT capasity FROM travelbureau_carts WHERE type= cart) as capasity FROM trader
                     WHERE username=:username";
        $stmt2 = $this->db->conn->prepare($sql2);
        $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt2->execute();
        $data['trader_data'] = $stmt2->fetch(PDO::FETCH_ASSOC);

        // If trader assignment is not 0 then the player has a trading assignment
        if ($data['trader_data']['assignment_id'] != 0) {
            $param_assignment_id = $data['trader_data']['assignment_id'];
            $sql4 = "SELECT base, destination, cargo, assignment_amount, assignment_type
                         FROM trader_assignments
                         WHERE assignment_id=:assignment_id";
            $stmt4 = $this->db->conn->prepare($sql4);
            $stmt4->bindParam(":assignment_id", $param_assignment_id, PDO::PARAM_INT);
            $stmt4->execute();
            $row4 = $stmt4->fetch(PDO::FETCH_ASSOC);
            $data['trader_data']['base'] = $row4['base'];
            $data['trader_data']['destination'] = $row4['destination'];
            $data['trader_data']['cargo'] = $row4['cargo'];
            $data['trader_data']['assignment_amount'] = $row4['assignment_amount'];
            $data['trader_data']['assignment_type'] = $row4['assignment_type'];
        }

        $sql = "SELECT date_inserted FROM merchant_offers LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $data['merchantTimes'] = $stmt->fetch(PDO::FETCH_ASSOC);

        // Select latest date
        $sql = "SELECT MAX(date_inserted) as max_date_inserted FROM trader_assignments";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $data['traderAssignmentTime'] = $stmt->fetch(PDO::FETCH_ASSOC);
        $datetime_now = date_timestamp_get(new DateTime(date("Y-m-d H:i:s")));

        // Get trader offers
        // $data['offers'] = $this->getOffers();

        // If 4 hours has passed, make new trader assignments
        if (
            date_timestamp_get(new DateTime($data['traderAssignmentTime']['max_date_inserted'])) + 14400 <
            $datetime_now || $data['traderAssignmentTime']['max_date_inserted'] === NULL
        ) {
            $data['trader_assignments'] = $this->makeTraderAssignments();
        }

        $sql5 = "SELECT assignment_id, base, destination, cargo, assignment_amount, time, assignment_type,
                        date_inserted
                        FROM trader_assignments
                        WHERE date_inserted = (SELECT MAX(date_inserted) FROM trader_assignments)";
        $stmt5 = $this->db->conn->prepare($sql5);
        $stmt5->execute();
        $data['trader_assignments'] = $stmt5->fetchAll(PDO::FETCH_ASSOC);

        $location = $this->session['location'];

        $data['trader_assignments'] = $this->sortTraderAssignments($data['trader_assignments'], $location);

        // $data['trader_assignments'] = array_reverse($data['trader_assignments']);
        $data['gold'] = $this->session['gold'];

        // if statement to check if ajax request is being called
        if ($js === true) {
            ob_start();
            get_template('merchantStock', $data, true);
            $this->response->addTo("html", ob_get_clean());
        } else {
            return $data;
        }
    }
    public function makeTraderAssignments()
    {
        $small_trades_amount = 5;
        $medium_trades_amount = 3;
        $large_trades_amount = 2;
        $favor_amount = 5;

        $trader_assignments = array();
        $favor_index = 0;
        for ($i = 1; $i < ($small_trades_amount + $medium_trades_amount + $large_trades_amount + $favor_amount + 1); $i++) {
            $assignment = array();
            switch ($i) {
                case ($i <= $small_trades_amount):
                    // Make assignment with easy difficulty
                    $assignment['assignment_type'] = "small";
                    $assignment['assignment_amount'] = rand(60, 100);
                    $assignment['reward'] = 200;
                    $assignment['time'] = 600;
                    break;
                case ($i <= ($small_trades_amount + $medium_trades_amount)):
                    // Make missions with medium difficulty
                    $assignment['assignment_type'] = "medium";
                    $assignment['assignment_amount'] = rand(120, 300);
                    $assignment['reward'] = 500;
                    $assignment['time'] = 500;
                    break;
                case ($i <= ($small_trades_amount + $medium_trades_amount + $large_trades_amount)):
                    // Make missions with hard difficulty
                    $assignment['assignment_type'] = "large";
                    $assignment['assignment_amount'] = rand(350, 700);
                    $assignment['reward'] = 1200;
                    $assignment['time'] = 400;
                    break;
                case ($i >= ($small_trades_amount + $medium_trades_amount + $large_trades_amount)):
                    // Make favor missions
                    $assignment['assignment_type'] = "favor";
                    $assignment['assignment_amount'] = rand(150, 200);
                    $assignment['reward'] = 0;
                    $assignment['time'] = 0;
                    break;
            }
            if ($assignment['assignment_type'] === "favor") {
                // Slice location array to filter out the locations that doesnt have favor assignments
                $locations = array_slice($this->locations, 7);
                $base = $assignment['base'] = $locations[$favor_index];

                $destinations = array_filter(array_slice($this->locations, 0, 6), function ($var) use ($base) {
                    return ($var != $base);
                });
                $assignment['destination'] = $destinations[array_rand($destinations)];
                $favor_index++;
                $sql = "SELECT name FROM items WHERE in_game = 1 AND store_value > 0 ORDER BY RAND() LIMIT 1";
                $stmt = $this->db->conn->prepare($sql);
            } else {
                $locations = array_slice($this->locations, 0, 6);
                $base = $assignment['base'] = $locations[array_rand($locations)];
                $destinations = array_filter($locations, function ($var) use ($base) {
                    return ($var != $base);
                });
                $assignment['destination'] = $destinations[array_rand($destinations)];
                $param_assignment_type = $assignment['assignment_type'];
                $sql = "SELECT name FROM items WHERE in_game = 1 AND store_value > 0 AND trader_assignment_type=:assignment_type
                            ORDER BY RAND() LIMIT 1";
                $stmt = $this->db->conn->prepare($sql);
                $stmt->bindParam(":assignment_type", $param_assignment_type, PDO::PARAM_STR);
            }
            $stmt->execute();
            $assignment['cargo'] = $stmt->fetch(PDO::FETCH_OBJ)->name;
            $trader_assignments[] = $assignment;
        }
        // Delete old assigments which isn't active and which is not current datetime
        $sql = "DELETE a FROM trader_assignments AS a
                    LEFT JOIN trader AS b ON a.assignment_id = b.assignment_id 
                    JOIN 
                        (SELECT MAX(date_inserted) as date_inserted 
                        FROM trader_assignments GROUP BY date_inserted) 
                    AS c ON a.date_inserted < c.date_inserted 
                    WHERE b.assignment_id IS NULL";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        try {
            $this->db->conn->beginTransaction();

            $param_base = "";
            $param_destination = "";
            $param_cargo = "";
            $param_assignment_amount = "";
            $param_time = "";
            $param_assignment_type = "";

            // Insert new trades
            $sql = "INSERT INTO trader_assignments 
                        (base, destination, cargo, assignment_amount, time, assignment_type)
                        VALUES
                        (:base, :destination, :cargo, :assignment_amount, :time, :assignment_type)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":base", $param_base, PDO::PARAM_STR);
            $stmt->bindParam(":destination", $param_destination, PDO::PARAM_STR);
            $stmt->bindParam(":cargo", $param_cargo, PDO::PARAM_STR);
            $stmt->bindParam(":assignment_amount", $param_assignment_amount, PDO::PARAM_INT);
            $stmt->bindParam(":time", $param_time, PDO::PARAM_INT);
            $stmt->bindParam(":assignment_type", $param_assignment_type, PDO::PARAM_STR);
            foreach ($trader_assignments as $key => $value) {
                $param_base = $value['base'];
                $param_destination = $value['destination'];
                $param_cargo = $value['cargo'];
                $param_assignment_amount = $value['assignment_amount'];
                $param_time = $value['time'];
                $param_assignment_type = $value['assignment_type'];
                $stmt->execute();
            }

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->db->conn->rollBack();
            $this->errorHandler->reportError(array($this->username, $e->getMessage()));
            return false;
        }
    }
    public function makeTrades()
    {
        $location_stores = array();
        $locations = array_slice($this->locations, 1);
        for ($i = 0; $i < count($locations); $i++) {
            $location = $locations[$i];
            $db_table_name = $location . '_rate';
            $db_table_name = str_replace("-", "_", $db_table_name);

            // Select 4 items which has store_rate of 1 in the specified location
            $sql = "SELECT name, store_value, {$db_table_name} FROM items 
                        WHERE {$db_table_name} = 1 AND in_game = 1 AND store_value > 0
                        ORDER BY RAND() LIMIT 7";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Select 4 items which has store_rate of 3 in the specified location
            $sql = "SELECT name, store_value, {$db_table_name} FROM items 
                        WHERE {$db_table_name} = 2 AND in_game = 1 AND store_value > 0
                        ORDER BY RAND() LIMIT 3";
            $stmt = $this->db->conn->prepare($sql);
            $row2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Select 4 items which has store_rate of 3 in the specified location
            $sql = "SELECT name, store_value, {$db_table_name} FROM items 
                        WHERE {$db_table_name} = 3 AND in_game = 1 AND store_value > 0
                        ORDER BY RAND() LIMIT 2";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            $row3 = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $items = array_merge($row1, $row2, $row3);
            for ($x = 0; $x < count($items); $x++) {
                $random_item = $items[$x];
                switch (intval($items[$x][$db_table_name])) {
                    case 1:
                        $random_item['amount'] = rand(4, 10);
                        break;
                    case 2:
                        $random_item['amount'] = rand(2, 7);
                        break;
                    case 3:
                        $random_item['amount'] = rand(1, 5);
                        break;
                    case 4:
                        $random_item['amount'] = rand(1, 2);
                        break;
                    default:
                        $random_item['amount'] = 0;
                        break;
                }
                $random_item['store_rate'] = $items[$x][$db_table_name];
                // First add the store_rate variable to price, decimal value
                $random_item['user_buy_price'] = floor($random_item['store_value'] *
                    (1 + ($random_item['store_rate'] / 30)));

                // If there are few items add extra to the price
                $random_item['user_buy_price'] = floor($random_item['user_buy_price'] +
                    ($random_item['user_buy_price'] / 50 * (1 - ($random_item['amount'] * 0.10)))
                    + rand(
                        $random_item['store_rate'] * 0.05,
                        $random_item['store_rate'] * 0.10
                    ));
                $location_stores[$location][] = $random_item;
            }
        }
        try {
            $this->db->conn->beginTransaction();
            // Delete old trades
            $sql = "DELETE FROM merchant_offers";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute();
            // If no rows has been affected, throw error;
            if ($stmt->rowCount() === 0) {
                throw new Exception("No rows deleted from delete query " . __METHOD__);
            }

            // Insert new trades
            $param_location = "";
            $param_item = "";
            $param_user_buy_price = "";
            $param_user_sell_price = "";
            $param_amount = "";
            $sql = "INSERT INTO merchant_offers (location, item, store_value, sell_value, amount)
                        VALUES(:location, :item, :user_buy_price, :user_sell_price, :amount)";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":user_buy_price", $param_user_buy_price, PDO::PARAM_STR);
            $stmt->bindParam(":user_sell_price", $param_user_sell_price, PDO::PARAM_INT);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
            foreach ($location_stores as $key => $value) {
                $param_location = $key;
                for ($i = 0; $i < count($value); $i++) {
                    $param_item = $value[$i]['name'];
                    $param_user_buy_price = $value[$i]['user_buy_price'];
                    $param_user_sell_price = $value[$i]['user_buy_price'] * 0.97;
                    $param_amount = $value[$i]['amount'];
                    $stmt->execute();
                }
            }

            $this->db->conn->commit();
        } catch (Exception $e) {
            $this->db->conn->rollBack();
            $this->errorHandler->reportError(array($this->username, $e->getMessage()));
            return false;
        }
    }
    public function getMerchantCountdown($js = false)
    {
        $sql = "SELECT date_inserted FROM merchant_offers LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $date = date_timestamp_get(new DateTime($row['date_inserted']));
        $this->response->addTo("data", $date, array("index" => "date"));
        $this->db->closeConn();
    }
    public function getTraderAssigmentCountdown($js = false)
    {
        $sql = "SELECT date_inserted FROM trader_assignments ORDER BY date_inserted DESC LIMIT 1";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $date = date_timestamp_get(new DateTime($row['date_inserted']));
        $this->response->addTo("data", $date, array("index" => "traderAssigmentCountdown"));
        $this->db->closeConn();
    }
    public function getOffers($js = false)
    {
        $param_location = $this->session['location'];
        $sql = "SELECT item, store_value, sell_value, amount    
                FROM merchant_offers 
                WHERE location=:location";
        $stmt = $this->db->conn->prepare($sql);
        $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
        $stmt->execute();
        $data['offers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $data['diplomacy'] = array();
        $locations = array("hirtam", "pvitul", "khanz", "ter", "fansal-plains");
        if (in_array($this->session['location'], $locations)) {
            $param_username = $this->username;
            $sql = "SELECT hirtam, pvitul, khanz, ter, fansalplains FROM diplomacy WHERE username=:username";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->execute();
            $data['diplomacy'] = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if ($js === true) {
            ob_start();
            get_template('merchantOffers', $data, true);
            $this->response->addTo("html", ob_get_clean());
        } else {
            return $data;
        }
    }
    // public function tradeItem($POST)
    // {
    //     // $POST variable holds the post data
    //     // This function is called from an AJAX request from merchant.js
    //     // Function to trade item with merchant

    //     $Diplomacy_model = Diplomacy_model::getSelf();

    //     $item = strtolower($POST['item']);
    //     $amount = $POST['amount'];
    //     $mode = $POST['mode'];
    //     if ($this->session['location'] !== 'fagna') {
    //         $param_location = $this->session['location'];
    //         $param_item = $item;
    //         $sql = "SELECT item, user_buy_price, user_sell_price, amount 
    //                     FROM merchant_offers WHERE location=:location AND item=:item";
    //         $stmt = $this->db->conn->prepare($sql);
    //         $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
    //         $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
    //         $stmt->execute();
    //         $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //         // If the item doesn't exists and the method is sell
    //         if ($stmt->rowCount() == 0) {
    //             if ($mode == 'sell') {
    //                 $this->response->addTo("errorGameMessage", "The merchant isn't interested in what you are trying to sell");
    //                 return false;
    //             } else {
    //                 $this->response->addTo("errorGameMessage", "The merchant isn't selling what you are trying to buy");
    //                 return false;
    //             }
    //         }
    //         if ($mode == 'buy' && $row['amount'] < $amount) {
    //             $this->response->addTo("errorGameMessage", "The merchant isn't selling the amount you are trying to buy");
    //             return false;
    //         }
    //     }

    //     // User is selling items
    //     if ($mode == 'sell') {
    //         // Check if city is fagna
    //         if ($this->session['location'] == "fagna") {
    //             $param_name = $item;
    //             // Find price for fagna;
    //             $sql = "SELECT store_value FROM items WHERE name=:name";
    //             $stmt = $this->db->conn->prepare($sql);
    //             $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
    //             $stmt->execute();
    //             $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //             $total_sell_price = $amount * $row['store_value'];
    //         } else {
    //             // 5% reduction in price if price is over 1500
    //             $reduction = ($row['user_buy_price'] > 1500) ? 0.95 : 0.97;
    //             $minimum_price = $row['user_sell_price'] * ((1 - $reduction) + 1);
    //             $new_merchant_buy_price = $row['user_buy_price'];

    //             for ($i = 0; $i < $amount; $i++) {
    //                 $new_merchant_buy_price *= $reduction;
    //             }
    //             if ($new_merchant_buy_price < $minimum_price) $new_merchant_buy_price = $minimum_price;
    //             $total_sell_price = $row['user_sell_price'] * $amount;
    //             $new_merchant_buy_price = floor($new_merchant_buy_price);
    //         }

    //         $param_username = $this->username;
    //         $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";
    //         $stmt = $this->db->conn->prepare($sql);
    //         $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
    //         $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
    //         $param_item = $item;
    //         $param_username = $this->username;
    //         $stmt->execute();
    //         $row2 = $stmt->fetch(PDO::FETCH_ASSOC);
    //         if (!$stmt->rowCount() > 0) {
    //             $this->response->addTo("errorGameMessage", "You don't have item you are trying to sell");
    //             return false;
    //         }
    //         $new_amount = $amount + $row2['amount'];
    //     } else {
    //         // $mode == 'buy'
    //         if ($Diplomacy_model->isDiplomacyLocation($this->session['location'])) {
    //             $diplomacy_price_adjust = 1;
    //             $location = str_replace("-", "", $this->session['location']);
    //             $diplomacy_price_ratio = $Diplomacy_model->user_diplomacies[$location];
    //             // Check diplomacy_price_ratio
    //             if ($diplomacy_price_ratio > 1.2) {
    //                 $diplomacy_price_adjust = 0.1;
    //             } else if ($diplomacy_price_ratio > 1) {
    //                 $diplomacy_price_adjust = ($diplomacy_price_ratio - 1) / 2;
    //             } else {
    //                 $diplomacy_price_adjust = (1 - $diplomacy_price_ratio) / 2;
    //             }
    //             $buy_price = round(($diplomacy_price_ratio < 1) ?  $row['user_buy_price'] * (1.0 + $diplomacy_price_adjust) :
    //                 $row['user_buy_price'] * (1.0 - $diplomacy_price_adjust));
    //             $buy_price = round(($buy_price > $row['user_sell_price']) ? $buy_price : $row['user_sell_price']);
    //         } else {
    //             $buy_price = $row['user_buy_price'];
    //         }
    //         $new_amount = $row['amount'] - $amount;
    //         $total_price = $row['user_buy_price'] * $amount;
    //         $new_merchant_buy_price = $row['user_buy_price'];
    //         $increase = ($row['user_buy_price'] > 1500) ? 1.05 : 1.03;
    //         // Increase price by 3 %
    //         for ($i = 0; $i < $amount; $i++) {
    //             $new_merchant_buy_price *= $increase;
    //         }

    //         $Diplomacy_model->calculateNew(0.5, $location);
    //     }
    //     try {
    //         $this->db->conn->beginTransaction();
    //         if ($mode == 'sell') {
    //             // Update inventory
    //             $this->UpdateGamedata->updateInventory($item, -$amount);
    //             $this->UpdateGamedata->updateInventory('gold', $total_sell_price, true);
    //             if ($this->session['location'] !== "fagna") {
    //                 $param_user_buy_price = $new_merchant_buy_price;
    //                 $param_amount = $new_amount;
    //                 $param_location = $this->session['location'];
    //                 $param_item = $item;
    //                 $sql = "UPDATE merchant_offers SET user_buy_price=:user_buy_price, amount=:amount
    //                         WHERE location=:location AND item=:item";
    //                 $stmt = $this->db->conn->prepare($sql);
    //                 $stmt->bindParam(":user_buy_price", $param_user_buy_price, PDO::PARAM_INT);
    //                 $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
    //                 $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
    //                 $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
    //                 $stmt->execute();
    //             }
    //         } else {
    //             // $mode == 'buy'
    //             // Update inventory
    //             $this->UpdateGamedata->updateInventory($item, $amount);
    //             $this->UpdateGamedata->updateInventory('gold', -$total_price, true);

    //             $param_user_buy_price = $new_merchant_buy_price;
    //             $param_amount = $new_amount;
    //             $param_location = $this->session['location'];
    //             $param_item = $item;
    //             $sql = "UPDATE merchant_offers SET user_buy_price=:user_buy_price, amount=:amount
    //                         WHERE location=:location AND item=:item";
    //             $stmt = $this->db->conn->prepare($sql);
    //             $stmt->bindParam(":user_buy_price", $param_user_buy_price, PDO::PARAM_INT);
    //             $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
    //             $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
    //             $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
    //             $stmt->execute();
    //         }

    //         if ($Diplomacy_model->isDiplomacyLocation($this->session['location'])) {


    //             $this->response->addTo("gameMessage", "Diplomacy relations have been updated! See diplomacy tab");
    //         }

    //         $this->db->conn->commit();
    //     } catch (Exception $e) {
    //         $this->response->addTo("errorGameMessage", $this->errorHandler->catchAJAX($this->db, $e));
    //         return false;
    //     }

    //     ob_start();
    //     get_template('merchantOffers', $this->getOffers(), true);
    //     $this->response->addTo("html", ob_get_clean());
    //     $this->db->closeConn();
    // }

    /**
     * Get price from item
     *
     * @param string $item to get price
     *
     * @return array result
     */
    // public function getPrice(string $item)
    // {
    //     $param_name = $item;

    //     $sql = "SELECT store_value FROM items WHERE name=:name";
    //     $stmt = $this->db->conn->prepare($sql);

    //     $stmt->bindParam(":name", $param_name, PDO::PARAM_STR);
    //     $stmt->execute();

    //     return $stmt->fetch(PDO::FETCH_OBJ)->store_value;
    // }

    protected function sortTraderAssignments($trader_assignments, $location)
    {
        function getDifficultyValue($difficulty)
        {
            switch ($difficulty) {
                case 'small':
                    return 4;
                case 'favor':
                    return 3;
                case 'medium':
                    return 2;
                case 'large':
                    return 1;
            }
            return 5;
        }
        usort($trader_assignments, function ($a, $b) use ($location) {
            $a_check = ($a["base"] === $location) ? 1 : -1;
            $b_check = ($b["base"] === $location) ? 1 : -1;
            return
                [$b_check, getDifficultyValue($b['assignment_type'])]
                <=>
                [$a_check, getDifficultyValue($a['assignment_type'])];
        });
        return $trader_assignments;
    }
}
