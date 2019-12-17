<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name;?>.css" />
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS');?>select.css" />
    </head>
    <body>

        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php');?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?> 
            <h3 class="page_title"> Mine </h3>
            <div id="action_div">
                <div id="actions">
                    <p id="mining"></p>
                    <p id="time"></p>
                    <button id="cancel"> Cancel Mining </button>
                </div>
                <div id="select">
                    <?php
                    foreach($this->data['mineral_types'] as $key): ?>
                    <img class="mineral" src="<?php echo constant('ROUTE_IMG') . $key['mineral_type'] . ' ore.png';?>"
                    alt="<?php echo $key['mineral_type'];?>"/>
                    <?php endforeach;?>
                </div>
                <div id="data_container">
                    <p>Total permits: <?php echo $this->data['minerData']['permits'];?></p>
                    <div id="data">
                        <form id="data_form">
                            <figure></figure>
                            <div class="row">
                                <label for="mineral"> Mineral: </label>
                                <input type="text" name="mineral" readonly /></br>
                            </div>
                            <label for="time"> Time: </label>
                            <input type="text" name="time" readonly /></br>
                            <label for="permit"> Permit: </label>
                            <input type="text" name="permit" readonly /></br>
                            <label for="workforce"> Workforce:</label>
                            <input name="workforce" type="number" min="0" required />
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
            <script src="<?php echo constant("ROUTE_JS") . 'select.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php');?>
        </aside>
    </body>
</html>
