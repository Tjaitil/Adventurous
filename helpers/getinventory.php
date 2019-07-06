<?php
    function getinventory($conn, $username) {
        $sql = "SELECT item, amount FROM inventory WHERE username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $username;
        $stmt->execute();
        $_SESSION['gamedata']['inventory'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>