<!DOCTYPE html>
<html>
    <head>
        <title> <?php echo $title ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name;?>.css" />
        <?php require('views/head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
         <?php
        ?> 
        <header>
            <?php include('header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="farm">
                <span id="farm_head"> Farm: </span>
                </br>
                <div id="crops">
                 <a href="/crops" onmouseover="showcaseCrops();" onmouseout="hideCrops();"> Crops </a></br>
                    <div id="crops_card">
                        <div id="crop_type"> Crop type: <?php
                        $userInfo->cropType;
                        ?></div>
                        <div id="crop_countdown"> Time remaining: &nbsp <p id="crop_countdown"></p>
                       </div>
                        
                        <div id="crop_workforce"> Workers used: <?php $userInfo->cropWorkforced;
                        $workforce = $userInfo->cropWorkforced;
                        for ($x = 0; $x < $workforce; $x++) {
                            echo 'i';
                        }
                        ?></div>
                    </div>
                </div>

                <div id="butchery">
                 <a href="/butchery" onmouseover="showcaseButchery();" onmouseout="hideButchery();"> Butchery </a></br>
                    <div id="butchery_card">
                        <div id="butchery_type"></div>
                        <div id="progress_bar">
                            <div id="progress_bar2"> </div>
                            <div id="progress_bar3"> </div>
                           
                        </div>
                    </div>
                </div>
                <div id="workforce">
                 <a href="/workforcelodge" onmouseover="showcaseWorkforce();" onmouseout="hideWorkforce();"> Workforce Lodge </a></br>
                    <div id="workforce_card">
                        Available workforce:
                        <div id="workforce_bar">
                            <div id="workforce_bar2">  </div>
                            <div id="workforce_bar3"><?php echo $_SESSION['gamedata']['avail_workforce'];?><span id="workforce_ava"></span>
                            / <span id="workforce_tot"></span><?php echo $_SESSION['gamedata']['workforce_total'];?></div>
                        </div>
                        </div>
                </div>
            </div>
           <script src="<?php echo constant("ROUTE_JS") . $name;?>.js"></script>
        </section>
        <aside>
            <?php include('aside.php');?>
        </aside>
        <!-- <script src="troop.js"></script>-->
    </body>            
</html>