<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php require(constant('ROUTE_VIEW') . '/head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="trades">
                <div>
                <?php get_template('merchantStock', $this->data); ?>
                </div>
                <div id="do_trade">
                    <div id="selected_trade">
                        
                    </div>
                    <label for="amount"> Amount </label></br>
                    <input type="number" id="amount" name="amount" /></br>
                    <label for="bond"> Use bond? </label></br>
                    <input type="checkbox" id="bond" name="bond" /></br>
                    <button> Trade </button>
                </div>
            </div>
            <div id="inventory">
                    <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
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
                            <th> Assignment Type </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tr>
                        <?php get_template('assignment', $this->data);?>
                    </tr>
                </table>
                <div id="assignment">
                    <p>Cart Capasity: <?php echo $this->data['trader_data']['cart_amount'] , '/', $this->data['trader_data']['capasity'];?></p>
                    <p>Current assignment: <?php echo $this->data['trader_data']['assignment'];?></p>
                    <div id="pick_up">
                        <button onclick="pickUp();">Pick up items</button>
                    </div>
                    <div id="deliver">
                        <button onclick="deliver();">Deliver</button>
                    </div>
                </div>
                <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php');?>
        </aside>
    </body>
</html>