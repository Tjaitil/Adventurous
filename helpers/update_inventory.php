<?php
    function updateinventory($conn, $username, $item, $quantity) {
            $sql = "SELECT item, amount FROM inventory WHERE item=:item AND username=:username";  
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $username;
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $count = $stmt->rowCount();
            if ($count === 0) {
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
            else if($count > 0) {
                // If items already exists in bank
                $sql2 = "UPDATE inventory SET amount=:amount WHERE username=:username AND item=:item";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                $param_amount = $quantity + $row['amount'];
                $param_username = $username;
                $param_item = $item;
                $stmt2->execute();
            }
            if(!$param_amount > 0) {
                //If item is zero
                $sql = "DELETE FROM inventory WHERE item=:item AND username=:username";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                $param_item = $item;
                $param_username = $username;
                $stmt->execute();
            }
    }
    
?>