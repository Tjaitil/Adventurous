<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            <div>
                <a href="/armycamp"> << Back to army camp</a>
            </div>
            <div id="warriors">
                <?php get_template('armory', $this->data['warrior_armory']) ;?>
            </div>
            <div id="put_on">
                <div id="selected">
                </div>
                <select id="select_warrior">
                    <option></option>
                    <?php foreach($this->data['warrior_armory'] as $key): ?>
                    <option><?php echo $key['warrior_id'];?></option>
                    <?php endforeach;?>
                </select>
                <select id="type">
                    <option value="right"> Right hand </option>
                    <option value="left"> Left hand </option>
                </select>
                <button onclick="wearArmor();"> Put on </button>
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . "inventory.php"); url();?>
            </div>
            <script src="<?php echo constant('ROUTE_JS'). 'selectitem.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
