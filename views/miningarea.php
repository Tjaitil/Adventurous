<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <p id="">Mining</p></br>
            <a href="/mine"> Mine</a>
            <a href="/workforcelodge"> Workforce lodge </a>
            Mining countdown:
            <p id="time"></p>
            
            <script src="<?php echo constant("ROUTE_JS") . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php');?>
        </aside>
    </body>
</html>
