<?php

use App\libs\TemplateFetcher;

?>
<div id="log_pseudo_parent">

</div>
<input type="checkbox" name="" id="draw_checkbox">
<div id="log_container" class="div_content mb-1">
    <div id="log" class="darkTextColor">
        <table id="game_messages">
            <?php
            // echo TemplateFetcher::loadTemplate('log', $_SESSION['log'] ?? []);
// ?>
        </table>
    </div>
</div>
<div id="log_2"></div>
<?php

// if (count($_SESSION['gamedata']['level_up']) > 0) :
?>
<!-- News is the black "curtain" which the content is displayed upon
     both used for alert and news -->
<div id="news">
</div>
<div id="news_content">
    <div id="news_content_side_panel">

    </div>
    <div id="news_content_main_content" class="mb-2 mt-2">

    </div>
    <img class="cont_exit" src="{{ asset('images/exit.png'); }}" width="20px" height="20px" />
</div>