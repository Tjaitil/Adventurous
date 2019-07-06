<!DOCTYPE HTML>
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="stylesheets/ad.sttemplate.css" />
        <link rel="stylesheet" type="text/css" href="#" />
        <meta charset="utf-8"/>
        <title> Troops </title>
    </head>
    <body>
         <?php
           session_start();
            require_once('root/config.php');
            if(!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header("location: /Adventurous/KB.php");
            exit;
           }
           include('root/root/config.php');
           include('classes/inc.getworkforce.php');
           include('classes/inc.butchery.php');
           include('classes/inc.butcheryact.php');
           $sesUser = $_SESSION['username'];
           $workforceInfo = new getWorkforce($sesUser);
           $getbutcherydata = new getButcheryData();
           $getbutcherydata->fetchData($sesUser);
           $butcheryact = new butcheryact($sesUser);
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
            </br>
            <table>
                <thead>
                    <tr>
                        <td>Animals</td>
                        <td>Fullgrown quant</td>
                        <td>Growing quant</td>
                        <td>Countdown to finsihed growing</td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Chickens</td>
                        <td>?</td>
                        <td>?</td>
                        <td>?</td>
                    </tr>
                    <tr>
                        <td>Pigs</td>
                        <td>?</td>
                        <td>?</td>
                        <td>?</td>
                    </tr>
                    <tr>
                        <td>Cows</td>
                        <td>?</td>
                        <td>?</td>
                        <td>?</td>
                    </tr>
                </tbody>
            </table>
            <table>
                
            </table>
            <div id="actions">
                <div>
    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <label for="type"> Animal type: </label>
                        <select name="type" id="">
                            <option>?</option>
                        </select><p class="error"><?php echo $butcheryact->typeErr;?></p></br>
                        <label for="quant"> Select amount of animals: </label>
                        <input name="quant" id="" type="number" min="0"/><p class="error"><?php echo $butcheryact->quantErr;?></p></br>
                        <label for="workforce"> Select amount of workers:</label>
                        <input name="workforce" id="" type="number" min="0"/><p class="error"><?php echo $butcheryact->workforceErr;?></p>
                        <label for="breed"> Breed:</label>
                        <input name="action" id="" value="breed" type="radio" />
                        <label for="slaught"> Slaughter: </label>
                        <input name="action" id="" value="slaught" type="radio" /></br>
                        <p class="error"><?php echo $butcheryact->selectErr;?></p>
                        <button name="Submit" id="" type="submit">Submit</button>
                    </form>
                </div>
                <a href="#"> Buy animals</a>
            </div>
        </section>
        <aside>
            <div id="side_menu">
                <h5> Categories</h5>
                <ul>
                    <li><a href="#">Rules</a></li>
                    <li><a href="#">Forum</a></li>
                    <li><a href="#">Latest patch</a></li>
                    <li><a href="gameguide.php">Gameguide</a></li>
                    <li><a href="#">FAQ & help</a></li>
                    <li><a href="#">1</a></li>
                    <li><a href="#">1</a></li>
                    <li><a href="#">1</a></li>
                </ul>
            </div>
        </aside>
</html>