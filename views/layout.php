<?php
    require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
    urlcheck();
?>
<!--onmouseenter="get_xp('farmer', this);" onmouseout="get_xp('farmer', this);"-->
<div id="skills">
    <div onclick="get_xp('farmer', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['farmer']['level']; ?></span>
        <span class="tooltip"></span>
    </div>
    <div onclick="get_xp('miner', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['miner']['level']; ?></span>
        <span class="tooltip"></span>
    </div>
    <div onclick="get_xp('trader', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['trader']['level']; ?></span>
        <span class="tooltip"></span>
    </div>
    <div onclick="get_xp('warrior', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['warrior']['level']; ?></span>
        <span class="tooltip"></span>
    </div>
    <div>
        <img src="#" />
        <span> Adventurer: </span><?php echo 1; ?>
        <span class="tooltip"></span>
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
<?php if(strlen($_SESSION['gamedata']['game_message']) > 3):?>
    <script>getgMessage();</script>
<?php endif;?>