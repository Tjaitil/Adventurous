<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <div id="new_offer">
                <form id="register_offer">
                    <label> Item: </label>
                    <input type="text" name="item" />
                    <label for="amount"> Amount </label>
                    <input type="number" name="amount" min="0" /></br>
                    <label for="price_ea"> Price ea </label>
                    <input name="price_ea" type="number" /></br>
                    <label for="total_price"> Total price </label>
                    <input name="total_price" type="number" readonly />
                    <button> Register offer </button>
                </form>
            </div>
            <div id="history">
                
            </div>
            <div id="graphs">
                
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
