<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name ?>.css" />
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <div id="farmer">
                <table id="highscores">
                    <caption> Farmer <img src="<?php echo constant('ROUTE_IMG'); ?> . 'farmer.jpg'"></caption>
                    <thead>
                        <tr>
                            <td> Name: </td>
                            <td> Level: </td>
                            <td> Experience </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['farmer_highscores'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['username']); ?></td>
                        <td><?php echo $key['farmer_level']; ?></td>
                        <td><?php echo $key['farmer_xp']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
            <div id="miner">
                <table>
                    <caption> Miner <img src="<?php echo constant('ROUTE_IMG'); ?> . 'Miner.jpg'"></caption>
                    <thead>
                        <tr>
                            <td> Name: </td>
                            <td> Level: </td>
                            <td> Experience </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['miner_highscores'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['username']); ?></td>
                        <td><?php echo $key['miner_level']; ?></td>
                        <td><?php echo $key['miner_xp']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
            <div id="trader">
                <table>
                    <caption> Trader <img src="<?php echo constant('ROUTE_IMG'); ?> . 'trader.jpg'"></caption>
                    <thead>
                        <tr>
                            <td> Name: </td>
                            <td> Level: </td>
                            <td> Experience </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['trader_highscores'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['username']); ?></td>
                        <td><?php echo $key['trader_level']; ?></td>
                        <td><?php echo $key['trader_xp']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
            <div id="warrior">
                <table>
                    <caption> Warrior <img src="<?php echo constant('ROUTE_IMG'); ?> . 'warrior.jpg'"></caption>
                    <thead>
                        <tr>
                            <td> Name: </td>
                            <td> Level: </td>
                            <td> Experience </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['warrior_highscores'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['username']); ?></td>
                        <td><?php echo $key['warrior_level']; ?></td>
                        <td><?php echo $key['warrior_xp']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
            <div id="total">
                <table id="highscores">
                    <caption> Total </caption>
                    <thead>
                        <tr>
                            <td> Name: </td>
                            <td> Level: </td>
                            <td> Experience </td>
                        </tr>
                    </thead>
                    <?php foreach($this->data['total_highscores'] as $key): ?>
                    <tr>
                        <td><?php echo ucfirst($key['username']); ?></td>
                        <td><?php echo $key['total_level']; ?></td>
                        <td><?php echo $key['total_xp']; ?></td>
                    </tr>
                    <?php endforeach;?>
                </table>
            </div>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . 'aside.php'); ?>
        </aside>
    </body>
</html>
