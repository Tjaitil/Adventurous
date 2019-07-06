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
            <?php if(strlen($_SESSION['gamedata']['game_message']) > 3):?>
                <script>getgMessage();</script>
            <?php endif;?>
            <p id=""> Mine</p></br>
            <p id=""><?php echo (isset($this->data['notification'])) ? $this->data['notification'] : ""; ?></p>
            <?php print_r($this->error);?>
            <p id="growing"></p>
            <p id="time"></p>
            <form method="post" action="" id="form">
                <label for="type"> Select mineral type:</label>
                <select name="type" id="form_select" onchange="img();">
                    <option selected="selected" value="0|0"></option>
                    <?php
                    foreach($this->data['mineral_types'] as $key): ?>
                    <option value="<?php echo $key['mineral_type'] . "|". $key['permit_cost'] ;?>">
                    <?php echo ucfirst($key['mineral_type']) ?></option>
                    <?php endforeach;?>
                </select><img src="#" id="type_img" /><p class="error"><?php echo $this->error['typeErr'];?></p></br>
                <label for="workforce"> Workforce:</label>
                <input name="workforce" id="" type="number" min="0" />
                <p class="error"><?php echo $this->error['workforceErr']; ?></p></br>

                <p class=""></p>
                (<?php echo $this->data['workforceData']['avail_workforce']?>)
                <p>Total permits: <?php echo $this->data['minerData']['permits'];?></p>
                <p class="error"><?php echo $this->error['permitErr']?></p>
                <p class="error"><?php echo $this->error['workErr']; ?></p>
                <button type="submit"> Mine </button>
            </form>
            <script src="<?php echo constant("ROUTE_JS") . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
