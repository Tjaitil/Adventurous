<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="public/css/main.css" />
        <link rel="stylesheet" type="text/css" href="public/css/layout.css" />
        <link rel="stylesheet" type="text/css" href="public/css/header.css" />
        <link rel="stylesheet" type="text/css" href="public/css/sidebar.css" />
        <meta charset="utf-8"/>
        <title> Troops </title>
    </head>
    <body>
         <?php
           session_start();
            require_once('root/config.php');
            if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header("location: /login.php");
            exit;
           }
        ?> 
        <header>
            <div class="top_bar">
                <div class="a" onmouseover="hover();" onmouseout="unHover();"><a href="main.php">3</a></div>
                <div class="but" onmouseover="hover();" onmouseout="unHover();"><a href="main.php">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="#" class="bar_city"> 1 </a></div>
                <div class="but"><a href="#" class="bar_city"> City </a></div>
            </div>
            <div class="top_bar">
                <div class="a"><a href="travel.php">2</a></div>            
                <div class="but"><a href="travel.php">Travel</a></div>
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
        </header>
        <section>
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
            <img src="map.png" id="world_map" height="300px" width="500px"/>
            <!--- <script src="main.js"></script> -->
            <p id="demo"></p>
            
            
            <!--<script>
                
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
            </script> -->
            
            <div id="profile">
                <span id="profile_header"> Player Card</span></br>
                <img id="profile_picture" src="" height="50%" width="74%" /></br>
                <span id="profile_profiency"><?php echo $row['Profiency']; ?></span>
                <div id="skill_bar">
                    
                    <div id="skill_bar2">
                        
                    </div>
                    <div id="skill_bar_progress"><?php echo $row['Experience'];?>&nbsp/&nbsp<?php echo $row['Next Level']; ?></div>
                    <?php echo $row['Level']; ?>
                </div></br>
                <script>
                    window.onload = function xpWidth() {
                        xmlhttp = new XMLHttpRequest();
                        xmlhttp.open("GET", "/adventurous/getxp.php");
                        xmlhttp.send();
                        xmlhttp.onload = function () {
                            if(this.readyState == 4 && this.status == 200) {
                                x = [];
                                x.push(this.responseText);
                                var x_value1 = Number(x[0]);
                                var x_value2 = Number(x[1]);
                                var width = x_value1 / x_value2 * 100;
                                console.log(width);
                                document.getElementById("skill_bar2").style.Width = width + "%"; 
                            }
                        };
                    };
                </script>
                <a href="#"> View more profile details >></a>
                <p id="demo"></p>
            </div>
            
            <div id="town_map">
                <p id="town">Currently staying in:</p><?php echo ucfirst($row['City']);?></br>
                <a id="town_map_a" href="#">
                    <img src="" width="300" height="200" style="border:1px solid black;"/>
                </a>
                <script src="cityhandler.js">
                </script>
            </div>
            <div id="ressources_view">
                <table id="ressource_table">
                    <thead>
                        <tr>
                            <th>Type:</th>
                            <th>Quantity: (Including all types)</th>
                        </tr>
                        <tr>
                            <td>Gold</td>
                            <td>?</td>
                        </tr>
                        <tr>
                            <td>Artefacts</td>
                            <td>?</td>
                        </tr>
                        <tr>
                            <td> Foods </td>
                            <td> ? </td>
                        </tr>
                        <tr>
                            <td> Minerals</td>
                            <td>    ?</td>
                        </tr>
                    </thead>
                </table>
                </br>
                <a href="#"> Check out full storage:</a>
            </div>
            <div id="upgrades">
                Upgrades
            </div>
            
            
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