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
            <p id="">Workforce Lodge</p></br>
            <?php var_dump($this->data);?>
            <div id="workers">
                Current workforce level: <?php echo $this->data['workforce_level'];?></br>
                Workforce capasity: <?php echo $this->data['workers']['workforce_total'], '/', $this->data['workforce_building']['max_workers'];?>
                </br>
                <?php  /*foreach($this->data['workers'] as $key): ?>
                    <div class="worker">
                        <ul>
                            <li> Number: <?php echo $key['worker_number'];?></li>
                            <li> Level: <?php echo $key['level'];?></li>
                            <li> Task: <?php echo $key['task'];?></li>
                        </ul>
                    </div>
                <?php endforeach;*/?>
            </div>
            <a href="/tavern"> Hire more workers </a>
            <a href="/laboratory"> Upgrade workers </a>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
