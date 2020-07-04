<?php
    require_once(constant('ROUTE_HELPER') . 'urlcheck.php');
    urlcheck();
?>
<div id="skills">
    <div onclick="get_xp('adventurer', this);">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . 'adventurer icon.png';?>" />
            <figcaption id="ad_tooltip"><?php echo $_SESSION['gamedata']['adventurer_respect'];?></figcaption>
        </figure>
        <span class="skill_tooltip"></span>
        <span></span>
    </div>
    <div onclick="get_xp('farmer', this);">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png';?>" />
            <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['farmer']['level'];?></figcaption>
        </figure>
        <span class="skill_tooltip"></span>
        <!-- Span to display xp when gained -->
        <span></span>
    </div>
    <div onclick="get_xp('miner', this);">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png';?>" />
            <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['miner']['level'];?></figcaption>
        </figure>
        <span class="skill_tooltip"></span>
        <!-- Span to display xp when gained -->
        <span></span>
    </div>
    <div onclick="get_xp('trader', this);">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png';?>" />
            <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['trader']['level'];?></figcaption>
        </figure>
        <span class="skill_tooltip"></span>
        <!-- Span to display xp when gained -->
        <span></span>
    </div>
    <div onclick="get_xp('warrior', this);">
        <figure>
            <img src="<?php echo constant('ROUTE_IMG') . 'warrior icon.png';?>" />
            <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['farmer']['level'];?></figcaption>
        </figure>
        <span class="skill_tooltip"></span>
        <!-- Span to display xp when gained -->
        <span></span>
    </div>
</div>
<div id="log_pseudo_parent">
    
</div>
<div id="log">
    <table id="game_messages">
        <?php if(count($_SESSION['log']) > 0) {
            get_template('log', $_SESSION['log']);
        };?>
    </table>
</div>
<div id="hunger">
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
    <span class="hunger_state"></span>
</div>
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
</div>
<div id="sidebar">
        <img id="cont_exit" src="#"  width="20px" height="20px" onclick="closeNews();" />
        <button onclick="sidebar.toggleSidebar();"> Click</button>
        <img id="cont_exit" src="#" />
        <button class="sidebar_button">Adventure</button>
        <button class="sidebar_button">2</button>
        <button class="sidebar_button">3</button>
        <div class="sidebar_tab" id="tab_1">
            1
        </div>
        <div class="sidebar_tab" id="tab_2">
            2
        </div>
        <div class="sidebar_tab" id="tab_3">
            3
        </div>
    </div>
