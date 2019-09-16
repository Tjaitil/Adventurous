<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?> 
            <h3 id="page_title"> Mine </h3>
            <p id="mining"></p>
            <p id="time"></p>
            <button id="cancel"> Cancel Mining </button>
            <div id="mine_action">
                <div id="mineral_select">
                    <?php
                    foreach($this->data['mineral_types'] as $key): ?>
                    <img class="mineral" src="<?php echo constant('ROUTE_IMG') . $key['mineral_type'] . ' ore.png';?>"
                    alt="<?php echo $key['mineral_type'];?>"/>
                    <?php endforeach;?>
                </div>
                <div id="mine_data">
                    <p>Total permits: <?php echo $this->data['minerData']['permits'];?></p>
                    <div id="mineral_data">
                        <form id="mine_form">
                            <label for="mineral"> Mineral: </label>
                            <input type="text" name="mineral" readonly /></br>
                            <label for="time"> Time: </label>
                            <input type="text" name="time" readonly /></br>
                            <label for="permit"> Permit: </label>
                            <input type="text" name="permit" readonly /></br>
                            <label for="workforce"> Workforce:</label>
                            <input name="workforce" id="" type="number" min="0" required />
                            <span>(<?php echo $this->data['workforceData']['avail_workforce']?>)</span>
                            <button type="button"> Mine </button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <script src="<?php echo constant("ROUTE_JS") . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
