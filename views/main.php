<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant("ROUTE_CSS") . $name; ?>.css" />
        <?php include(constant("ROUTE_VIEW") . 'head.php');?>
        <meta charset="utf-8"/>
    </head>
    <body>
        <header>
            <?php require(constant("ROUTE_VIEW") . 'header.php');?>
            <script src="public/js/time.js"></script>
        </header>
        <section>
            <?php require(constant("ROUTE_VIEW") . 'layout.php');?>
            <div id="public_chat">
            <div id="chat">
                <ul>
                    <?php get_template('chat', $this->data['chat']);?>            
                </ul>
            </div>
            
            <button onclick="show_xp('farmer', 10);"> Show_xp </button>
            <input type="text" id="text" />
            <button type="button" onclick="chat();"> Chat </button>
            </div>
            <img src="map.png" id="world_map" height="300px" width="500px"/>
            
            <p id="demo"></p>
            
            <div id="profile">
                <span id="profile_header"> Player Card</span></br>
                <img id="profile_picture" src="" height="50%" width="74%" /></br>
                <span id="profile_profiency"><?php echo ucfirst($_SESSION['gamedata']['profiency']); ?></span>
                <div id="skill_bar">
                    
                    <div id="skill_bar2">
                        
                    </div>
                    <div id="skill_bar_progress"><span id="progress_value1"><?php echo $_SESSION['gamedata']['profiency_xp'];?></span>
                    &nbsp/&nbsp<span id="progress_value2"><?php echo $_SESSION['gamedata']['profiency_xp_nextlevel']; ?></span></div>
                    <?php echo $_SESSION['gamedata']['profiency_level']; ?>
                </div></br>
                <a href="#"> View more profile details >></a>
                <p id="demo"></p>
            </div>
            <div id="town_map">
                <p id="town">Currently staying in:</p><?php echo ucfirst($_SESSION['gamedata']['location']);?></br>
                <a id="town_map_a" href="#">
                    <img src="" width="300" height="200" style="border:1px solid black;"/>
                </a>
                <!--- <script src="cityhandler.js"> --->
                </script>
            </div>
            <div id="countdowns">
                <table>
                    <caption> Countdowns: </caption>
                    <?php var_dump($this->data['countdowns']);?>
                </table>
            </div>
            <div id="diplomacy">
                <table>
                    <caption> Diplomacy: </caption>
                    <thead>
                        <tr>
                            <td> Location: </td>
                            <td> Diplomacy: </td>
                        </tr>
                    </thead>
                    <tr>
                        <td> Hirtam </td>
                        <td><?php echo $this->data['diplomacy']['hirtam'];?></td>
                    </tr>
                    <tr>
                        <td> Pvitul </td>
                        <td><?php echo $this->data['diplomacy']['pvitul'];?></td>
                    </tr>
                    <tr>
                        <td> Khanz </td>
                        <td><?php echo $this->data['diplomacy']['khanz'];?></td>
                    </tr>
                    <tr>
                        <td> Ter </td>
                        <td><?php echo $this->data['diplomacy']['ter'];?></td>
                    </tr>
                    <tr>
                        <td> Fansal Plains </td>
                        <td><?php echo $this->data['diplomacy']['fansalplains'];?></td>
                    </tr>
                    </tr>
                </table>
            </div>
            <script src="<?php echo constant('ROUTE_JS');?>main.js"></script>
        </section>
        <aside>
            <?php
                require(constant('ROUTE_VIEW') . 'aside.php');
            ?>
        </aside>
    </body>            
</html>