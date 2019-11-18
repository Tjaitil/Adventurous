<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title;?></title>
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            </br>
            <table>
                <thead>
                    <tr>
                        <td> Type: </td>
                        <td> Require: </td>
                        <td> Cost: </td>
                        <td> Food units: </td>
                        <td></td>
                    </tr>
                </thead>
                <tr>
                    <td><div class="item">
                            <figure onclick="show_title(this, false);">
                                <img src="<?php echo constant('ROUTE_IMG') . 'cooked potato' . '.png';?>" />
                                <figcaption class="tooltip"><?php echo ucwords('cooked potato'); ?></figcaption>
                            </figure>
                        </div>
                    </td>
                    <td> Potato <img src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 50 <img src="<?php echo constant('ROUTE_IMG') . '';?>" /></td>
                    <td> 1 </td>
                    <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                </tr>
                <tr>
                    <td> Flour </td>
                    <td> Wheat </td>
                    <td> 3 </td>
                    <td> - </td>
                    <td><input type="number" min="0" /><button onclick="make('Cooked Potato', this);"> Make </button></td>
                </tr>
            </table>
            <div id="inventory">
                <div id="layer">
                </div>
                <?php require(constant('ROUTE_VIEW') . '/inventory.php'); url();?>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
