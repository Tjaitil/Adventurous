<?php 
    class merchant extends controller  {
        public $data;
        
        function __construct() {
            parent::__construct();    
        }
        
        public function index() {
            $this->loadModel('Merchant', true);
            $this->data = $this->model->getData();
            $this->determineAssignment();
            /*if(new DateTime($data['merchantTimes']) < date("Y-m-d")) {
                /*$this->makeTrades();
            }*/
            
            $this->render('merchant', 'Merchant', $this->data, true);
        }
        private function determineAssignment() {
            // Check if there a trader assignment and if it is then format the string
            if($this->data['trader_data']['assignment_id'] != 0) {
                $format = "Carrying %s from %s to %s, delivered %d/%d (%s)";
                
                $this->data['trader_data']['assignment'] = sprintf($format, ucwords($this->data['trader_data'][0]['cargo']),
                                                                            ucwords($this->data['trader_data'][0]['base']),
                                                                            ucwords($this->data['trader_data'][0]['destination']),
                                                                            $this->data['trader_data']['delivered'],
                                                                            $this->data['trader_data'][0]['assignment_amount'],
                                                                            ucwords($this->data['trader_data'][0]['assignment_type']));
            }
            else {
                $this->data['trader_data']['assignment'] = "none";
            }
        }
        private function makeTrades() {
            $items = array();
            
            
            
            
        }
        private function makeAssignments() {
            $small_trades_amount;
            $small_trades = array();
            $medium_trades_amount;
            $medium_trades = array();
            $large_trades_amount;
            $large_trades = array();
            $favor_amount;
            $favors = array();
            
            $locations = array("cruendo", "golbak", "pvitul", "khanz", "ter", "hirtam", "fansal-plains", "tasnobil");
            $items[] = array("item" => "potato seed", "price" => 500,
                "locations" => locationItemData(array(
                "cruendo", "1", "1/10",
                "golbak", "4", "1/3",
                "hirtam", "4", "1/2")));
            $items[] = array("item" => "adron platebody", "price" => 4000,
                "locations" => locationItemData(array(
                "cruendo", "4", "1/3",
                "golbak", "2", "1/5")));
            /*$items[] = array("item" => "watermelon seed", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "wheat seed", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "adron ore", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));*/
            $items[] = array("item" => "iron ore", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "iron bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            $items[] = array("item" => "yeqdon bar", "value" => 20,
                               "locations" => locationItemData(array()));
            
                function locationItemData($array) {
                /* $array indexes every third is new place, [0] => "location", [1] => "store_rate", [2] => "amount"
                    * store_rate is a rating from 1 - 4 how rare the item is on the location
                    *
                    */
                    $new_array;
                    $x = 0;
                    for($i = 0; $i < floor(count($array) / 3); $i++) {
                    $new_array[] = array("location" => $array[$x], "store_rate" => 
                      $array[$x + 1], "amount" => $array[$x + 2]);
                    $x+= 3;
                    }
                    return $new_array;
                }
                $location_stores = array();
                for($i = 0; $i < 2; $i++) {
                    $location = $locations[$i];
                    echo $location;
                
                
                    $random_item = $items[array_rand($items)];
                    for($x = 0; $x < count($random_item['locations']); $x++) {
                        if($random_item['locations'][$x]['location'] == $location) {
                            $random_amount_array = explode("/", $random_item['locations'][$x]['amount']);
                            $random_item['amount'] = rand($random_amount_array[0], $random_amount_array[1]);
                            // store_rate
                            $random_item['store_rate'] = $random_item['locations'][$x]['store_rate'];
                            $random_item['org_price'] = floor($random_item['price']);
                            // First add the store_rate variable to price, decimal value
                            $random_item['price'] = floor($random_item['price'] *
                             (1 + ($random_item['locations'][$x]['store_rate'] / 30)));
                
                            // If there are few items add extra to the price
                            $random_item['price2'] = floor($random_item['price'] + 
                                                     ($random_item['price'] / 50 * (1 - ($random_item['amount'] * 0.10)))
                                                     + rand($random_item['locations'][$x]['store_rate'] / 125
                                                     , $random_item['locations'][$x]['store_rate'] / 150));
                            break;
                        }
                    }
                    unset($random_item['locations']);
                    $location_stores[$location][] = $random_item;
                    
                    // Insert
                    $sql = "INSERT INTO merchant_offers (location, item, price, amount
                            VALUES(:location, :item, :price, :amount)";
                    $stmt = $this->db->conn->prepare($sql);
                    $stmt->bindParam(":location", $param_location, PDO::PARAM_STR);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $stmt->bindParam(":price", $param_price, PDO::PARAM_INT);
                    $stmt->bindParam(":amount", $param_amount, PDO::PARAM_INT);
                    foreach($location_stores as $key => $value) {
                        $param_location = $key;
                        for($i = 0; $i < count($value); $i++) {
                            $param_item = $value[$i]['item'];
                            $param_price = $value[$i]['price'];
                            $param_amount = $value[$i]['amount'];
                            $stmt->execute();
                        }
                    }
                    
                    // Delete
                }
        }
    }
?>