<?php
    require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
    urlcheck();
?>
<!--onmouseenter="get_xp('farmer', this);" onmouseout="get_xp('farmer', this);"-->
<div>
<div id="skills">
    <div>
        <img src="#" />
        <span> Adventurer: </span><?php echo 1; ?>
        <span class="tooltip"></span>
        <span class="xp"></span>
    </div>
    <div onclick="get_xp('farmer', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['farmer']['level']; ?></span>
        <span class="tooltip"></span>
        <span class="xp"></span>
    </div>
    <div onclick="get_xp('miner', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['miner']['level']; ?></span>
        <span class="tooltip"></span>
        <span class="xp"></span>
    </div>
    <div onclick="get_xp('trader', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['trader']['level']; ?></span>
        <span class="tooltip"></span>
        <span class="xp"></span>
    </div>
    <div onclick="get_xp('warrior', this);">
        <img src="#" />
        <span><?php echo $_SESSION['gamedata']['warrior']['level']; ?></span>
        <span class="tooltip"></span>
        <span class="xp"></span>
    </div>
</div>
<div id="log">
    <table id="game_messages">
        <?php if(count($_SESSION['gamedata']['log']) > 0) {
            get_template('log', $_SESSION['gamedata']['log']);
        } ?>
    </table>
</div>
<?php
    $profiencies = array('farmer', 'miner', 'trader', 'warrior');
    
    $next_level = array($_SESSION['gamedata']['farmer']['xp'], $_SESSION['gamedata']['miner']['xp'],
                       $_SESSION['gamedata']['trader']['xp'], $_SESSION['gamedata']['warrior']['xp']);
    
    $_SESSION['gamedata']['level_up'] = array();
    for($i = 0; $i < count($profiencies); $i++) {
        if($_SESSION['gamedata'][$profiencies[$i]]['xp'] >= $_SESSION['gamedata'][$profiencies[$i]]['next_level']) {
            if($_SESSION['gamedata'][$profiencies[$i]]['level'] >= 30 && $_SESSION['gamedata']['profiency'] !== $profiencies[$i]) {
                continue;
            }
            else {
                array_push($_SESSION['gamedata']['level_up'], $profiencies[$i]);
            }
        }
    }
    if(count($_SESSION['gamedata']['level_up']) > 0): ?>
    <script src="<?php echo constant("ROUTE_JS") . 'levelup.js'?>"></script>
<?php endif;  ?>
</div>
<div id="news">
</div>
<div id="content">
    <img id="cont_exit" src="#"  width="20px" height="20px" onclick="closeNews();" />
</div>