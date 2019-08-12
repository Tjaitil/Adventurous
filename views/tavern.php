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
            <h3> Welcome to tavern, here you can recrute people</h3>
            <div id="persons">
                <?php if(empty($this->data['persons'])):?>
                <div> No persons of relevance in tavern </div>
                <?php endif; ?>
                <?php foreach($this->data['persons'] as $key): ?>
                    <div><img src="#" width="50px" height="50px" /><?php echo ucfirst($key); ?>
                    <button onclick="talk('<?php echo ucfirst($key); ?>', '0');">Talk</button></div>
                <?php endforeach;?>
            </div>
            </br>
            <?php get_template('tavern', $this->data); ?>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
                <div id="curtain">
                    <div id="conversation">
                    <img id="" src="#"  width="20px" height="20px" onclick="close();" />
                    <img id="conv_a" src="#" />
                    <p id="conv"></p>
                    <button id="conv_button" onclick="talk(0, '1');"> Click here to continue </button>
                    <img id="conv_b" src="#" />
                    </div>
                </div>
            <script src="<?php echo constant("ROUTE_JS"). $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php'); ?>
        </aside>
    </body>
</html>
