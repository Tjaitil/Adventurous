<?php
    function warrior_update($sql, $conn, $user) {
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $user, PDO::PARAM_STR);
        $stmt->execute();
    }
?>