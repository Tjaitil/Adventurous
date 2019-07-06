<?php
    $q = intval($_GET['q']);
    
    include('root/config.php');
    
    $sql = "UPDATE game_tjaitil SET troop = troop + 1";
    if ($link->query($sql) === TRUE) {
        $succes = true;
    }
    else {
        echo "</br> update failed";
    }
    $sql2 = "SELECT troop from game_tjaitil";
    if ($result = mysqli_query($link, $sql2)) {
        $row = mysqli_fetch_assoc($result);
        echo $row['troop'];
    }
?>