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
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <table id="horse_shop">
                <thead><tr>
                    <td>Horse type</td>
                    <td>Cost</td>
                    <td>Stock</td>
                    <td></td>
                </tr></thead>
                <?php get_template('horseShop', $this->data); ?>
            </table>
            <table id="cart_shop">
                <thead>
                    <tr>
                        <td> Cart wheel </td>
                        <td> Cart wood </td>
                        <td> Gold </td>
                        <td> Capasity </td>
                        <td> Stock </td>
                        <td> Mineral Amount </td>
                        <td> Wood Amount </td>
                        <td></td>
                    </tr>
                </thead>
                <?php get_template('cartShop', $this->data); ?>
            </table>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php');?>
        </aside>
    </body>
</html>
