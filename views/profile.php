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
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>

            <button onclick="show('password');"> Change Password </button>
            <div id="password">
                <form method="post" action="/profile">
                    <label for="current_password"> Current Password: </label>
                    <input name="current_password" type="password" required /></br>
                    <label for="new_password"> New Password: </label>
                    <input name="new_password" type="password" required tite /></br>
                    <label for="confirm_password"> Confirm Password: </label>
                    <input name="confirm_password" type="password" required title="Please conifrm your password" />
                    <button type="submit"> Change Password! </button> 
                </form>
            </div>
            
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
