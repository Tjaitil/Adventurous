<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS')?>skills.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require('views/header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="items">
                <?php get_template('merchantStock', $this->data); ?>
            </div>
            <div id="trader">
                <table>
                    <thead>
                        <tr>
                            <th> Delivery </th>
                            <th> Where </th>
                            <th> Cargo </th>
                            <th> Cargo Amount </th>
                            <th> Time </th>
                            <th> Reward </th>
                            <th> Assignment Type </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tr>
                        <?php get_template('assigment', $this->data); ?>
                    </tr>
                </table>
                <p>Cart Capasity: <?php echo $this->data['trader_data']['capasity'];?></p>
                <p>Current assignment: <?php echo $this->data['trader_data']['assignment'];?></p>
                <div id="pick_up">
                    <button onclick="pickUp();">Pick up items</button>
                </div>
                <div id="deliver">
                    <button onclick="deliver();">Deliver</button>
                </div>
                <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            </div>
        </section>
        <aside>
            <?php require('views/aside.php'); ?>
        </aside>
    </body>
</html>