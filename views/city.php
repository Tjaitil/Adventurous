<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php include(constant("ROUTE_VIEW") . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . $name; ?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . 'game'; ?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>conversation.css" />
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant("ROUTE_VIEW") . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="conversation_container">
                <img id="cont_exit" src="#"  width="20px" height="20px" onclick="closeNews();" />
                <h3></h3>
                  <img src="#" id="conversation_a" />
                  <div id="conversation">
                    <ul>
                
                    </ul>
                  </div>
                  <img src="#" id="conversation_b" />
            </div>
            <button id="inv_toggle_button"> INV </button>
            <canvas id="game_canvas" width="700" height="400"></canvas>
            <canvas id="game_canvas2" width="700" height="400"></canvas>
            <canvas id="game_canvas3" width="700" height="400"></canvas>
            <!--<canvas id="test_canvas" width="700" height="400"></canvas>-->
            <div id="game_text">

            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . '/inventory.php'); url();?>    
            </div>
            <div id="control">
                <button id="control_button"></button>
            </div>
            <div id="demo">
                
            </div>
            <div id="demo2" style="width:50px;height:50px;border:1px solid black;">
                
            </div>
            <script src="<?php echo constant('ROUTE_JS') .'game.js';?>"></script>
            <script src="<?php echo constant("ROUTE_JS") . 'selectitem.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'select.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'warriorSelect.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'conversation.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'travel.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
</html>
