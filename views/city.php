<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php include(constant("ROUTE_VIEW") . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . $name; ?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . 'game'; ?>.css" />
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant("ROUTE_VIEW") . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="city">
                <?php function buildingGenerator($city_data) {
                    
                    foreach($city_data as $key):
                        $key_merged = str_replace(" ", "", $key);
                        ?>
                        <a href="/<?php echo $key_merged;?>" id="city_<?php echo $key_merged;?>_link" class="building_link">
                            <img src="<?php echo constant('ROUTE_IMG') . $key_merged . ".png";?>" alt="<?php echo ucwords($key);?>" />
                        </a>
                    <?php endforeach;
                } 
                require($this->cityfile);?>
            </div>
            <p>Frames per second: <span id="frames"></span></p>
            <canvas id="game_canvas" width="700" height="400"></canvas>
            <button type="button" onkeydown="" onkeyup=""> Left </button>
            <button type="button" onkeydown="" onkeyup=""> Up </button>
            <button type="button" onkeydown="" onkeyup=""> Down </button>
            <button type="button" onkeydown="" onkeyup=""> Right </button>
            <script src="<?php echo constant('ROUTE_JS') .'game.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
</html>
