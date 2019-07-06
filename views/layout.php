<?php
    require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
    urlcheck();
?>
<div id="skills">
    <div onmouseenter="get_xp('farmer', this);">
        <img src="#" />
        <?php echo $_SESSION['gamedata']['farmer']['level']; ?>
    </div>
    <div onmouseenter="get_xp('miner', this);">
        <img src="#" />
        <?php echo $_SESSION['gamedata']['miner']['level']; ?>
    </div>
    <div onmouseenter="get_xp('trader', this);">
        <img src="#" />
        <?php echo $_SESSION['gamedata']['trader']['level']; ?>
    </div>
    <div onmouseenter="get_xp('warrior', this);">
        <img src="#" />
        <p> Warrior: </p><?php echo $_SESSION['gamedata']['warrior']['level']; ?>
    </div>
    <div>
        <img src="#" />
        <p> Adventurer: </p><?php echo 1; ?>
    </div>
</div>
<div id="log">
    <table id="game_messages">
        
    </table>
</div>
<?php
    $profiencies = array('farmer', 'miner', 'trader', 'warrior');
    
    $next_level = array($_SESSION['gamedata']['farmer']['xp'], $_SESSION['gamedata']['miner']['xp'],
                       $_SESSION['gamedata']['trader']['xp'], $_SESSION['gamedata']['warrior']['xp']);
    
    $_SESSION['gamedata']['level_up'] = array();
    for($i = 0; $i < count($profiencies); $i++) {
        if($_SESSION['gamedata'][$profiencies[$i]]['xp'] >= $_SESSION['gamedata'][$profiencies[$i]]['next_level']) {
            print($_SESSION['gamedata'][$profiencies[$i]]['level']);
            if($_SESSION['gamedata'][$profiencies[$i]]['level'] >= 30 && $_SESSION['gamedata']['profiency'] !== $profiencies[$i]) {
                echo "hello";
                continue;
            }
            else {
                array_push($_SESSION['gamedata']['level_up'], $profiencies[$i]);
            }
        }
    }
    if(count($_SESSION['gamedata']['level_up']) != 0): ?>
    <script src="<?php echo constant("ROUTE_JS") . 'levelup.js'?>"></script>
<?php endif;  ?>
<div id="news">
    <img id="" src="#"  width="20px" height="20px" onclick="closeNews();" />
</div>