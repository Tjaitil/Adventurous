<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS')?>conversation.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS");?>news.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <div id="wrapper">
            <header>
                <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
            </header>
            <section>
                <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
                <?php require(constant('ROUTE_VIEW') . $name . '.php');?>
                <?php if($name != 'advclient'): ?>
                    <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
                <?php endif;?>
                <script src="<?php echo constant('ROUTE_JS') . 'checkSite.js';?>"></script>
            </section>
            <?php if(array_search($name, array("main", "highscores", "news", "messages")) === false): ?>
            <aside>
                <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
            </aside>
            <?php endif;?>
            <footer>
                Delevoped by Kjetil Baksaas
            </footer>
        </div>
    </body>
</html>
