<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="public/css/<?php echo $name ?>.css" />
        <?php include('views/head.php');?>
    </head>
    <body>
        <header>
            <?php require('views/header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php');?>
            <?php var_dump($this->data); ?>
            <div id="farmer">
                <table id="highscores">
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
            <?php require('views/aside.php'); ?>
        </aside>
    </body>
</html>
