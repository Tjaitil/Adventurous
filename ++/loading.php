<html>
<head>
    <title> Loading!</title>
</head>
<?php
    include('session_start.php');
    include('config2.php');
    include('inc.loadgamedata.php');
    $loadGameData = new loadGameData($sesUser);
?>
<body>
    <h3> Loading game, please wait...</h3>
</body>
</html>