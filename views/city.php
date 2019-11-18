<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <?php include(constant("ROUTE_VIEW") . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . $name; ?>.css" />
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant("ROUTE_VIEW") . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="city">
                <?php require($this->cityfile);?>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
</html>
