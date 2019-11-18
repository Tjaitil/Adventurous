<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $title ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            <h3 class="page_title"> Travel</h3>
            <p id="travel"> </p>
            <p id="travel_time"> </p>
            <div id="canvas_area">
                <button onclick="travel('towhar');"> Towhar </button>
                <button onclick="travel('golbak');"> Golbak </button>
                <button onclick="travel('snerpiir');"> Snerpiir </button>
                <button onclick="travel('krasnur');"> Krasnur </button>
                <button onclick="travel('tasnobil');"> Tasnobil </button>
                <button onclick="travel('cruendo');"> Cruendo </button>
                <button onclick="travel('fagna');"> Fagna </button>
                <button onclick="travel('hirtam');"> Hirtam </button>
                <button onclick="travel('pvitul');"> Pvitul </button>
                <button onclick="travel('khanz');"> Khanz </button>
                <button onclick="travel('ter');"> Ter </button>
                <button onclick="travel('fansal plains');"> Fansal plains </button>
            </div>
            <script src="<?php echo constant("ROUTE_JS") . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php'); ?>
        </aside>
    </body>
</html>
