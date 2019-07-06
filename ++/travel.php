<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="stylesheets/ad.sttemplate.css"
        <link rel="stylesheet" type="text/css" href="stylesheets/travel.css" />
        <meta charset="utf-8"/>
        <title> Troops </title>
    </head>
    <body>
         <?php
         include('session_start.php');
         $session = new session();
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
            <div class="top_bar">
                <div class="a" onmouseover="hover();" onmouseout="unHover();"><a href="main.php">3</a></div>
                <div class="but" onmouseover="hover();" onmouseout="unHover();"><a href="main.php">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#" class="bar_city"> 1 </a></div>
                <div class="but"><a href="#" class="bar_city"> City </a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#">2</a></div>            
                <div class="but"><a href="#">Travel</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#">3</a></div>
                <div class="but"><a href="#">Adventures</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#">4</a></div>
                <div class="but"><a href="#">Highscores</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#">5</a></div>
                <div class="but"><a href="#">Messenges</a></div>
            </div>
            <script>
                /*function hover() {
                    document.getElementsByClassName("a").style.boxShadow = "3px 3px 10px black";
                    document.getElementsByClassName("but").style.boxShadow = "3px 3px 10px black";
                }
                function unHover() {
                    document.getElementsByClassName("a").style.boxShadow = "3px 3px 5px black";
                    document.getElementsByClassName("but").style.boxShadow = "3px 3px 5px black";
                }
                /*document.getElementsByClassName("a").addEventListener("onmouseover", hover());
                document.getElementsByClassName("but").addEventListener("onmouseout", unhover());*/
            </script>
                <h3> Welcome&nbsp<?php echo htmlspecialchars($_SESSION['username']); ?>!</h3></br>
            <canvas id="city">
                
            </canvas>
            <a href="farm.php"> Farm</a>
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
    </body>            
</html>