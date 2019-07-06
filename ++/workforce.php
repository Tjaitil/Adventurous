<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="crops.css" />
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
           include('root/config.php');
           include('inc.getworkforce.php');
           $username = $_SESSION['username'];
           $workforceInfo = new getWorkforce($username);
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
                <div class="a"><a href="main.php">3</a></div>
                <div class="but"><a href="main.php">Main</a></div>
            </div>
            <div class="top_bar">
                <div class="a"><?php echo "<a href='#'>1</a>"?></div>
                <div class="but"><?php echo "<a href='#'>City</a>"?></div>
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
            <div id="back">
                <a href="/adventurous/farm.php"> Back to farm</a>
            </div>
            <h3> Workforce</h3>
                <table>
                    <tr>
                        <th>Workers used</th>
                        <th>On What</th>
                    </tr>
                    <tr>
                        <td>Crop</td>
                        <td><?php echo $workforceInfo->cropWorkforce;?></td>
                    </tr>
                    <tr>
                        <td>Butchery</td>
                        <td><?php echo $workforceInfo->butchWorkforce;?></td>
                    </tr>
                </table>
                
            <div>
                <a href="#">Increase workforce:</a>
                
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
        <!-- <script src="troop.js"></script>-->
    </body>            
</html>