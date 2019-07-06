<?php
    function updateXp($conn, $username, $profiency, $xp) {
        $profiencies = array("farmer", "miner", "trader", "warrior");
        if(in_array($profiency, $profiencies) == false) {
            return false;
        }
        $sql = "UPDATE user_levels SET " . $profiency . "_xp=:xp WHERE username=:username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":xp", $param_xp, PDO::PARAM_STR);
        $stmt->bindParam(":username", $param_username, PDO::PARAM_STR);
        $param_xp = $xp;
        $param_username = $username;
        $stmt->execute();
    }
    
?>