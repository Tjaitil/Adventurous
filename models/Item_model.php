<?php
    class Item_model extends model {
        public $username;
        public $session;
        public $query;
        
        function __construct ($session) {
            parent::__construct();
            $this->username = $session['username'];
            $this->session = $session;
        }
        
        public function checkItem($query) {
            $items = array("Potato Seed", "Tomato Seed", "Corn Seed", "Carrot Seed", "Cabbage Seed", "Wheat Seed", "Spices Seed",
                           "Sugar Seed", "Apple Seed", "Orange Seed", "Watermelon Seed", "Cooked Potato",
                           "Beech Bow", "Yew Bow",
                           "Iron Shield", "Iron Sword", "Steel Shield", "Gargonite Shield",
                           "Iron Platebody", "Birch", "Oak", "Yew");    
            
            /*function my_search($items) {
                $needle = ucwords($this->query);
                return(strpos($items, $needle) !== false); // or stripos() if you want case-insensitive searching.
            }*/
            /*$matches = array_filter($items, 'my_search');*/
            $query .= '%';
            $sql = "SELECT name FROM items WHERE name LIKE ?";
            $stmt = $this->db->conn->prepare($sql);
            $stmt->execute(array($query));
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            for($i = 0; $i < count($row); $i++) {
                echo $row[$i]['name'];
                if($i !== count($row) - 1) {
                    echo '|';
                }
            }
            /*$matches = array();
            foreach($items as $key) {
                if(strpos($key, $query) !== false) {
                    array_push($matches, $key);
                }
            }*/
            /*$matches = preg_grep($query, $items);*/
            /*js_echo($matches);*/
        }
    }
?>