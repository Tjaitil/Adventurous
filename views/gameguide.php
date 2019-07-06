<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $title; ?>.css" />
        <?php include('views/head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php include('views/header.php');?>
        </header>
        <section>
            
            <div id="welcome_butt">
            </div>
            <div id="updates">
                <h4> Important articles</h4>
                <ul>
                    <li><a href="#">Profiencies</a></li>
                    <li><a href="#">Farmer</a></li>
                    <li><a href="#">Miner</a></li>
                    <li><a href="#">Adventurer</a></li>
                    <li><a href="#">Warrior</a></li>
                    <li><a href="#">Trader</a></li>
                    <li><a href="#">1</a></li>
                </ul>
            </div>
            <div id="news">
                <h3>News:</h3>
            </div>
            <div id="profiencies">
                <span><b>Profiencies</b></span>
                <p>
                This is one of the fundamentals of the game. This is what you will first chose when you start the game.
                It is important to read carefully about the different profiences before jumping into game.
                Proficenes decide what type of player you become, your role and awards in adventures. It is possible to switch profiency, but this will come at a
                <a href="#">cost</a>.
                </br> The five profiencies to chose from are:
                <a href="#farmer">Farmer</a>, <a href="#">Trader</a>, <a href="#">Warrior</a> and <a href=#">Miner</a>       
                </p>
                </br>
            <div id="farmer">
                <span><b>Farmer</b></span>
                <p>
                Farmer profiency is about to collect food, This can be sold to citizens, chieftans or other players.
                In early levels farmer will only have control over a small farm. In some cities this will be not farm but fishing establishment.
                Farmer has potential to go on hunts and collect foods.
                In raids the farmer has oppurtinites to find massive food ressources and artefacts that boosts.
                The following table follows the upgrades farmer profiency can do:
                <table>
                   <thead>
                    <tr>
                        <th> What:</th>
                        <th> Level unlocked:</th>
                    </tr>
                   </thead>
                   <tbody>
                    <tr>
                        <td> Small farm </td>
                        <td> Giant farm </td>
                        <td>  </td>
                        <td> Giant farm </td>
                    </tr>
                    <tr>
                        <td> 3 </td>
                    </tr>
                   </tbody>
                   
                </table>
                </p>
                <div id="pages">
                    <ul>
                        <li> 1 </li>
                        <li> 2 </li>
                        <li> 3 </li>
                        <li> 4 </li>
                    </ul>
                </div>
            </div>
        </section>
        <aside>
            <?php include('views/aside.php');?>
        </aside>
        <!-- <script src="troop.js"></script>-->
    </body>            
</html>