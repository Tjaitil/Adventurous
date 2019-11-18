<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <?php require(constant('ROUTE_VIEW') . 'head.php');?>
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
            <h3 id="page_title"> Army Missions </h3>
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
                    <tr>
                        <td><?php echo ucfirst($key['location']);?></td>
                        <td><?php echo $key['required_warriors'];?></td>
                        <td><?php echo $key['mission'];?></td>
                        <td>
                            <?php echo $key['reward'];?>
                            <img width="15" height="15" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>"/>
                        </td>
                        <td><?php echo $key['time'];?></td>
                        <td><button onclick="prepareMission(this, <?php echo $key['mission_id']; ?>);"> Do mission</button></td>
                    </tr>
                    <?php endforeach;?>
                </thead>
            </table>
            <div id="mission_enabled">
                <button onclick="exit();">Exit</button>
                <table id="mission_table">
                                            
                </table>
                <p>Select Warriors:</p>
                <div id="warriors_container">
                    
                </div>
                <button id="mission_button" onclick="doMission();"> Do Mission </button>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
