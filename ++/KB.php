<!DOCTYPE HTML>
<HTML>
<head>
    <title> Home </title>
    <link rel="stylesheet" type="text/css" href="stylesheets/KB.css"/>

</head>
<body>
    <section>
        <?php
           session_start();
           
           if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header("location: /adventurous/login.php");
            exit;
           }
        ?>
        <div>
            <img id="logo" src="pictures/TJ Portals.png" />
            <p>
                Welcome <?php echo htmlspecialchars($_SESSION['username']); ?>!</br>
                TJ Portals is a collection of many different sites, check them out below!
            </p>
            <ul>
                <li><a href="KB.php"> Home </a>
                <li><a href="History.php"> History </a>
                <li><a href="music.php">Music</a>
                <li><a href="#">4</a>
                <li><a href="#">5</a>
                <li><a href="#">6</a>
                <li><a href="#">7</a>
                <li><a href="#">8</a>
                <li><a href="troop.php">Troop</a>
                <li><a href="#">10</a>
            </ul>
        </div>

        <div>
            </br>
            <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        </div>
    </section>
</body>
</HTML>
