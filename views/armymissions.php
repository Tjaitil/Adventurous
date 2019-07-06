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
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            <div>
                <a href="/armycamp"> << Back to army camp</a>
            </div>
            <p id="">Army Missions</p></br>
            <div id="current_mission">
            <p> Mission:</p><p id="time"></p>
            </div>
            <table>
                <thead>
                    <tr>
                        <td> Location </td>
                        <td> Warriors required </td>
                        <td> Mission </td>
                        <td> Reward </td>
                        <td> Time </td>
                        <td> Time </td>
                    </tr>
                    <?php foreach($this->data['armyMissions'] as $key):?>
                    <?php $i = 0;?>
                    <tr>
                        <td><?php echo $key['location'];?></td>
                        <td><?php echo $key['required_warriors'];?></td>
                        <td><?php echo $key['mission'];?></td>
                        <td>
                            <?php echo $key['reward'][$i * 1 + 1];?>
                            <img width="15" height="15" src="<?php echo constant('ROUTE_IMG') . $key['reward'][$i * 1] . '.jpg';?>"/>
                        </td>
                        <td><?php echo $key['time'];?></td>
                        <td><button onclick="prepareMission(this, <?php echo $key['mission_id']; ?>);"> Do mission</button></td>
                    </tr>
                    <?php $i++;?>
                    <?php endforeach;?>
                </thead>
            </table>
            <div id="mission">
                <div id="mission_enabled">
                    <button onclick="exit();">Exit</button>
                    <table id="mission_table">
                                                
                    </table>
                    <p>Select Warriors:</p>
                    
                    <button id="mission_button" onclick="doMission();"> Do Mission </button>
                </div>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
