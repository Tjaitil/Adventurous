<?php
    function updatestockpile($conn, $username, $item, $quantity) {
            $sql = "SELECT item FROM stockpile WHERE item=:item AND username=:username";  
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_item = $item;
            $param_username = $username;
            $stmt->execute();
            $row = $stmt->rowCount();
            if ($row === 0) {
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
            else if($row > 0) {
                    // If items already exists in bank
                    $sql = "SELECT amount FROM stockpile WHERE username=:username AND item=:item";         
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_username = $username;
                    $param_item = $item;
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $sql2 = "UPDATE stockpile SET amount=:amount WHERE username=:username AND item=:item";
                    $stmt2 = $conn->prepare($sql2);
                    $stmt2->bindParam(":amount", $param_amount, PDO::PARAM_STR);
                    $stmt2->bindParam(":username", $param_username, PDO::PARAM_STR);
                    $stmt2->bindParam(":item", $param_item, PDO::PARAM_STR);
                    $param_amount = $quantity + $row['amount'];
                    $param_username = $username;
                    $param_item = $item;
                    $stmt2->execute();
            }
        }
?>.