<?php

use App\libs\TemplateFetcher;

?>

<!--   
    <div id="side_menu">
        <h5> Categories</h5>
        <ul>
            <li><a href="#">Rules</a></li>
            <li><a href="#">Forum</a></li>
            <li><a href="#">Latest patch</a></li>
            <li><a href="/gameguide">Gameguide</a></li>
            <li><a href="#">FAQ & help</a></li>
        </ul>
    </div>
    <div id="side_menu2">
        
        
    </div>
    -->
<div id="sidebar">
    <button id="sidebar_button_toggle" class="sidebar_button">
        << </button>
            <p><?php echo ucfirst($_SESSION['username']); ?></p>
            <p class="mt-1 mb-1"><?php echo ucfirst($data['profiency']); ?></p>
            <p><?php echo ucwords($data['location']); ?></p>
            <button class="sidebar_button">Adventure
                <?php
                // echo ($this->data['adventure']['adventure_data']['notification'] == 1) ? '(!)' : "" 
                ?></button>
            <button class="sidebar_button">Countdowns</button>
            <button class="sidebar_button">Diplomacy</button>
            <button class="sidebar_button">Skills</button>
            <div class="sidebar_tab" id="tab_1">

            </div>
            <div class="sidebar_tab lightTextColor" id="tab_2">
                <?php
                echo TemplateFetcher::loadTemplate('profiencyStatus', $data['profiency_status']);
                // get_template('profiencyStatus', $this->data['profiency_status']);
                ?>
            </div>
            <div class="sidebar_tab" id="tab_3">
                <?php
                // echo TemplateFetcher::loadTemplate('diplomacy', $data['diplomacy_data'])
                ?>
            </div>
            <div class="sidebar_tab darkTextColor" id="tab_4">
                <div id="skills" class="mt-1">
                    <div class="profiency-level-wrapper darkTextColor" data-wrapper-skill="adventurer">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . 'adventurer icon.png'; ?>" />
                            <figcaption><?php echo $data['levels']['adventurer_respect']; ?></figcaption>
                        </figure>
                        <span class="skill_tooltip"></span>
                        <span></span>
                    </div>
                    <div class="profiency-level-wrapper darkTextColor" data-wrapper-skill="farmer">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png'; ?>" />
                            <figcaption class="skill_level"><?php echo $data['levels']['farmer_level']; ?></figcaption>
                        </figure>
                        <span class="skill_tooltip"></span>
                        <!-- Span to display xp when gained -->
                        <span></span>
                    </div>
                    <div class="profiency-level-wrapper darkTextColor" data-wrapper-skill="miner">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png'; ?>" />
                            <figcaption class="skill_level"><?php echo $data['levels']['miner_level']; ?></figcaption>
                        </figure>
                        <span class="skill_tooltip"></span>
                        <!-- Span to display xp when gained -->
                        <span></span>
                    </div>
                    <div class="profiency-level-wrapper darkTextColor" data-wrapper-skill="trader">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png'; ?>" />
                            <figcaption class="skill_level"><?php echo $data['levels']['trader_level']; ?></figcaption>
                        </figure>
                        <span class="skill_tooltip"></span>
                        <!-- Span to display xp when gained -->
                        <span></span>
                    </div>
                    <div class="profiency-level-wrapper darkTextColor" data-wrapper-skill="warrior">
                        <figure>
                            <img src="<?php echo constant('ROUTE_IMG') . 'warrior icon.png'; ?>" />
                            <figcaption class="skill_level"><?php echo $data['levels']['warrior_level']; ?></figcaption>
                        </figure>
                        <span class="skill_tooltip"></span>
                        <!-- Span to display xp when gained -->
                        <span></span>
                    </div>
                </div>
            </div>
</div>