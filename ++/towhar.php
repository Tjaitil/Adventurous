<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="stylesheets/ad.sttemplate.css" />
        <link rel="stylesheet" type="text/css" href="stylesheets/towhar.css" />
        <meta charset="utf-8"/>
        <title> Troops </title>
    </head>
    <body>
         <?php
           session_start();
            require_once('root/config.php');
            if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header("location: /adventurous/KB.php");
            exit;
           }
           ?>
        <header>
            <ul>
            <li><a href="KB.php"> Home </a>
            <li><a href="history.php"> History </a>
            <li><a href="music.php"> Music</a>
            <li><a href="#">4</a>
            <li><a href="#">4</a>
            <li><a href="#">4</a>
            <li><a href="#">4</a>
            <li><a href="#">4</a>
        </ul>
            
        </header>
        <section>
            <div id="top_bar">
                <div id="a"><a href="main.php">3</a></div>
                <div id="but"><a href="main.php">Main</a></div>
            </div>
            <div id="top_bar">
                <div id="a"><a href="#">1</a></div>
                <div id="but"><a href="#">City</a></div>
            </div>
            <div id="top_bar">
                <div id="a"><a href="travel.php">2</a></div>            
                <div id="but"><a href="travel.php">Travel</a></div>
            </div>
            <div id="top_bar">
                <div id="a"><a href="#">3</a></div>
                <div id="but"><a href="#">Adventures</a></div>
            </div>
            <div id="top_bar">
                <div id="a"><a href="#">4</a></div>
                <div id="but"><a href="#">Highscores</a></div>
            </div>
            <div id="top_bar">
                <div id="a"><a href="#">5</a></div>
                <div id="but"><a href="#">Messenges</a></div>
            </div>
            <h3> Welcome&nbsp<?php echo htmlspecialchars($_SESSION['username']); ?>!</h3></br>
            <canvas id="city">
                
            </canvas>
            <a href="farm.php"> Farm </a>
            <a href="mine.php"> Mine </a>
            <a href="guild.php"> Warrior Guild </a>
            <a href="storage.php"> Storage </a>
            <script src="towhar.js"></script>
        </section>
        <aside>
            <div id="side_menu">
            <h5> Categories</h5>
            <ul>
                <li><a href="#">Rules</a></li>
                <li><a href="#">Forum</a></li>
                <li><a href="#">Latest patch</a></li>
                <li><a href="#">Gameguide</a></li>
                <li><a href="#">FAQ & help</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">1</a></li>
            </ul>
        </div>
        </aside>
        <!-- <script src="troop.js"></script>-->
    </body>            
</html>