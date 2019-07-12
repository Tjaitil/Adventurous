<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $title ?>.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . '/header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <p id=""></p></br>
            <p id=""></p>
            <div id="stockpile">
                <?php get_template('stockpile', $this->data); ?>
            </div>
            <?php
                /*function amount($value) {
                    var_dump(strlen($value));
                    switch(true) {
                        case $value >= 1000:
                            echo "hello";
                            return $value / 1000 . 'k' ;
                            break;
                        default:
                            break;
                    }
                }
                echo amount(5000);*/
                ?>
            <div id="inventory">
                <p> Inventory: </p>
                <div id="hidden">
                        <div class="inventory_buttons">
                            <button onclick="insert(this, 1);"> 1 </button>
                            <button onclick="insert(this, 3);"> 3 </button>
                            <button onclick="insert(this, 5);"> 5 </button>
                            <button onclick="withdraw(this, 'all');"> All </button>
                        </div>
                        <figure><img src="#" height="50px" witdh="50px" />
                            <figcaption></figcaption>
                        </figure>
                                    
                    </div>
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
