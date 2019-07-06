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
            <p id=""></p></br>
            <p id=""></p>
            <table>
                <thead>
                    <tr>
                        <td> Type </td>
                        <td> Level </td>
                        <td> Cost </td>
                    </tr>
                </thead>
                <tr>
                    <td>  </td>
                    <td>  </td>
                    <td>  <img src="<?php echo constant('ROUTE_IMG') . 'gold.jpg';?>"/></td>
                    <td><button> Buy </button></td>
                </tr>
            </table>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
