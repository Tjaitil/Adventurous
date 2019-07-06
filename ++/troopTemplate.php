<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" hreF="troop.css" />
        <meta charset="utf-8"/>
        <title> Troops </title>
    </head>
    <body>
         <?php
           session_start();
            require_once('root/config.php');
            if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            echo "<script type='text/javascipt'> alert('You are not logged in')</script>";
            exit;
           }
           $username = $_SESSION['username'];
           $userErr = "";
            $sql = "SELECT troop FROM game_tjaitil";
            if ($result = mysqli_query($link, $sql)) {
                $row  = mysqli_fetch_assoc($result);
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
        
            <h3> Welcome&nbsp<?php echo htmlspecialchars($_SESSION['username']); ?>!</h3></br>
            
            <div id="welcome_butt">
                <a href="#"> New to the game?</a>&nbsp<a href="#">Coninue to game>></a></br></br>
            </div>
            <div id="updates">
                <h4> Latest updates!</h4>
                <ul>
                    <li>  Bug 1  </li>
                    <li>  Bug 2  </li>
                    <li>  Bug 3  </li>
                    <li>  Bug 4  </li>
                    <li>  Bug 5  </li>
                </ul>
            </div>
            <div id="news">
                <h3>News:</h3>
            </div>
             <?php echo $row['troop']; ?>
            <script>

                function showtroops () {
                    xmlhttp = new XMLHttpRequest();
                    xmlhttp.open("GET","/adventurous/gettroop.php?q=");
                    xmlhttp.send();
                    xmlhttp.onload = function (){
                        if(this.readyState == 4 && this.status == 200) {
                         document.getElementById("troop").innerHTML = this.responseText;   
                        }
                        };
                    
                } 
            </script>
            <p> Total troops are: </p><p id="troop">0</p>
            <button onclick="showtroops();"> Click to increase troops</button>
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