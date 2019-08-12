<?php
    function warrior_insert($warrior_amount, $city, $conn, $user) {
        
        $sql = "INSERT INTO tavern_warriors (username, city, type, level) VALUES(:username, :city, :type, :level)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $stmt->bindParam(":city", $param_city, PDO::PARAM_STR);
        $stmt->bindParam(":type", $param_type, PDO::PARAM_STR);
        $stmt->bindParam(":level", $param_level, PDO::PARAM_STR);
        $param_username = $user;
        $param_city = $city;
        $param_type = $warrior_amount['type'];
        $param_level = $warrior_amount['level'];
        $stmt->execute();
        // Was used in tavern_model
    }
?>