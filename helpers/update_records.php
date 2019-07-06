<?php
    function update_records($conn, $username, $offer_info, $type) {
        $sql = "SELECT username, amount FROM offer_records WHERE id=:id AND username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_id = $offer_info['id'];
        $param_username = $username;
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $stmt->rowCount();
        if($count === 0) {
            $sql = "INSERT INTO offer_records (id, username, type, item, amount, price_ea)
                    VALUES (:id, :username, :type, :item, :amount, :price_ea)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
            $stmt->bindParam(":item", $param_item, PDO::PARAM_STR);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt->bindParam(":price_ea", $param_price_ea, PDO::PARAM_STR);
            $param_id = $offer_info['id'];
            $param_username = $username;
            $param_type = $type;
            $param_item = $offer_info['item'];
            $param_amount = $offer_info['amount_left'];
            $param_price_ea = $offer_info['price_ea'];
            $stmt->execute();
        }
        else {
            $sql = "UPDATE offer_records SET amount=:amount WHERE id=:id AND username=:username";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":amount", $param_amount, PDO::PARAM_STR);
            $stmt->bindParam(":id", $param_id, PDO::PARAM_STR);
            $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
            $param_amount = $offer_info['amount'] + $row['amount'];
            $param_id = $offer_info['id'];
            $param_username = $username;
            $stmt->execute();
        }
    }
?>