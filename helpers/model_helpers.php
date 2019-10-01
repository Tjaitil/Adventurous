<?php
    function update_inventory($conn, $username, $item, $quantity, $update = false) {
        $sql = "SELECT amount FROM inventory WHERE item=:item AND username=:username";  
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_item = $item;
        $param_username = $username;
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if ($count === 0 && $quantity > 0) {
            // Insert new item into bank
            $sql = "INSERT INTO inventory (username, item, amount) VALUES(:username, :item, :amount)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_username = $username;
            $param_item = $item;
            $param_amount = $quantity;
            $stmt->execute();
        }
        $new_amount = ($count > 0 ) ? $row['amount'] + $quantity : $quantity;
        if ($count > 0 && $new_amount > 0) {
            // If items already exists in bank
            $sql2 = "UPDATE inventory SET amount=:amount WHERE username=:username AND item=:item";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_amount = $new_amount;
            $param_username = $username;
            $param_item = $item;
            $stmt2->execute();
        }
        else if(!$new_amount > 0) {
            //If item is zero
            $sql = "DELETE FROM inventory WHERE item=:item AND username=:username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $username;
            $stmt->execute();
        }
        //If multiple update_inventory are being made the update only happens at the last one
        if($update = true) {
            get_inventory($conn, $username);
        }
        if($item === 'gold') {
            $_SESSION['gamedata']['gold'] = get_item($_SESSION['gamedata']['inventory'], 'gold')['amount'];
        }
        unset($conn);
    }
    function update_stockpile($conn, $username, $item, $quantity) {
        $sql = "SELECT amount FROM stockpile WHERE item=:item AND username=:username";  
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_item = $item;
        $param_username = $username;
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if ($count === 0 && $quantity > 0) {
                // Insert new item into bank
            $sql = "INSERT INTO stockpile (username, item, amount) VALUES(:username, :item, :amount)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $param_username = $username;
            $param_item = $item;
            $param_amount = $quantity;
            $stmt->execute();

        }
        $new_amount = ($count > 0 ) ? $row['amount'] + $quantity : $quantity;
        
        if($count > 0 && $new_amount > 0) {
            // If items already exists in bank
            $sql2 = "UPDATE stockpile SET amount=:amount WHERE username=:username AND item=:item";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
            $param_amount = $row['amount'] + $quantity;
            $param_username = $username;
            $param_item = $item;
            $stmt2->execute();
        }
        if(!$new_amount > 0) {
            //If item is zero
            $sql = "DELETE FROM stockpile WHERE item=:item AND username=:username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $username;
            $stmt->execute();
        }
        unset($conn);
    }
    function get_inventory($conn, $username) {
        $sql = "SELECT item, amount FROM inventory WHERE username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $username;
        $stmt->execute();
        $_SESSION['gamedata']['inventory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    function update_xp($conn, $username, $profiency, $xp) {
        $profiencies = array("farmer", "miner", "trader", "warrior");
        if(in_array($profiency, $profiencies) == false) {
            return false;
        }
        
        $sql = "UPDATE user_levels SET {$profiency}_xp=:xp WHERE username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":xp", $param_xp, PDO::PARAM_INT);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_xp = $xp;
        $param_username = $username;
        $stmt->execute();
        $_SESSION['gamedata'][$profiency]['xp'] = $xp;
        unset($conn);
    }
    function get_item($inventory, $item) {
        foreach($inventory as $key) {
            if($key['item'] === $item) {
                return $key;
                break;
            }
        }    
    }
    function js_echo($data) {
        $count = count($data);
        $i = 0;
        foreach($data as $key) {
            $i++;
            if($i !== $count) {
                echo $key . "|";
            }
            else {
                echo $key;
            }
        }
    }
    function js_foreach($data) {
        foreach($data as $key) {
            foreach($key as $subkey) {
                echo $subkey . '|';
            }
            echo '|';
        }
    }
    function test($foo) {
        if($foo < 1) {
            echo "error";
            throw new Exception;
        }
    }
?>