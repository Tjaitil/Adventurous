<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo '../' . constant('ROUTE_CSS') . $name; ?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            
            <div id="welcome_butt">
            </div>
            <div>
                <?php require($this->guidefile);?>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>            
</html>