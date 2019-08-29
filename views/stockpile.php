<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . '/header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <p id=""></p></br>
            <p id=""></p>
            <div id="stockpile">
                <?php get_template('stockpile', $this->data); ?>
            </div>
            <div id="inventory">
                <p> Inventory: </p>
                <div id="hidden">
                        <div class="inventory_buttons">
                            <button onclick="insert(this, 1);"> 1 </button>
                            <button onclick="insert(this, 3);"> 3 </button>
                            <button onclick="insert(this, 5);"> 5 </button>
                            <button onclick="withdraw(this, 'all');" id="all"> All </button>
                        </div>
                        <figure><img src="#" height="50px" witdh="50px" />
                            <figcaption></figcaption>
                        </figure>
                                    
                    </div>
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <ul>
                <li>1</li>
                <li>2</li>
                
            </ul>
            <ul>
                <li ontouchstart="touchMove(this);">Insert 1</li>
                <li ontouchstart="touchMove(this);">Insert 5</li>
                <li ontouchstart="touchMove(this);">Insert x</li>
                <li ontouchstart="touchMove(this);">Insert all</li>
            </ul>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            <div id="stck_opt">
                    <!--
                    <button
                        onclick="insert(this, 1);"><div>1</div></button><!--
                    <button
                        onclick="insert(this, 5);"><div>5</div></button><!--
                    <button
                        onclick="insert(this, 'x');"><div>x</div></button><!--
                    <button
                        onclick="insert(this, 'all');" id="all">All</button><!--
                    -->
                    <ul>
                        <li></li>
                        <li ontouchstart="touchMove(this);">Insert 1</li>
                        <li ontouchstart="touchMove(this);">Insert 5</li>
                        <li ontouchstart="touchMove(this);">Insert x</li>
                        <li ontouchstart="touchMove(this);">Insert all</li>
                    </ul>
                </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
