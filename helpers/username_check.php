<?php
    function username_check($conn, $username) {
        $sql = "SELECT username FROM user_data WHERE username=:username";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_username = $username;
        $stmt->execute();
        if(!$stmt->rowCount() > 0) {
            return false;
        }
        else {
            return true;
        }
    }
?>