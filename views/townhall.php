<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <p> Current favor: <?php echo $_SESSION['gamedata']['favor'];?></p>
            <p> Current location: <?php echo $_SESSION['gamedata']['location']; ?></p>
            <p> Current diplomacy relation: <?php echo $this->data[$_SESSION['gamedata']['location']]; ?></p>
            <div id="favor">
                <p>Item: <?php echo $_SESSION['gamedata']['favor']['item'];?></p>
                <p>Amount: <?php echo $_SESSION['gamedata']['favor']['amount'];?></p>
                <p>Deliver to: <?php echo $_SESSION['gamedata']['favor']['destination']; ?></p>
                <button onclick="doFavor();"> Do favor</button>
            </div>
            <div id="pick_up">
                    <button onclick="pickUp();">Pick up items</button>
                </div>
                <div id="deliver">
                    <button onclick="deliver();">Deliver</button>
                </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'merchant.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
