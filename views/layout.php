<?php
    require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
    urlcheck();
?>
<div id="log_pseudo_parent">
    
</div>
<div id="log">
    <table id="game_messages">
        <?php if(count($_SESSION['log']) > 0) {
            get_template('log', $_SESSION['log']);
        };?>
    </table>
</div>
<div id="log_2"></div>
<?php
    if(count($_SESSION['gamedata']['level_up']) > 0): ?>
    <script src="<?php echo constant("ROUTE_JS") . 'levelup.js'?>"></script>
<?php endif;  ?>
    <script src="<?php echo constant("ROUTE_JS") . 'hunger.js'?>"></script>
<!-- News is the black "curtain" which the content is displayed upon
     both used for alert and news -->
<div id="news">
</div>
<div id="news_content">
    <img id="cont_exit" src="#"  width="20px" height="20px" onclick="closeNews();" />
    <div id="news_content_side_panel">
        
    </div>
    <div id="news_content_main_content">
        
    </div>
</div>
