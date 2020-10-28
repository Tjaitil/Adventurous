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
        <button onclick="sidebar.toggleSidebar();" id="sidebar_button_toggle"> << </button>
        <div id="hunger_bar">
            <div id="hunger_bar2">
        
            </div>
            <div id="hunger_bar_progress">
                <span class="progress_value1">0</span>
                &nbsp/&nbsp
                <span class="progress_value2">100</span>
            </div>
        </div>
        <button class="sidebar_button">Adventure</button>
        <button class="sidebar_button">Countdowns</button>
        <button class="sidebar_button">Diplomacy</button>
        <button class="sidebar_button">Skills</button>
        <div class="sidebar_tab" id="tab_1">
            <?php if($this->data['adventure']['current_adventure']['current'] == 0): ?>
                    <span> No current adventure! </span>
               <?php else: ?>
                    <div id="people">
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'farmer icon.png'; ?>" title="farmer"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['current_adventure']['info']['farmer']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'miner icon.png'; ?>" title="miner"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['current_adventure']['info']['miner']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'trader icon.png'; ?>" title="trader"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['current_adventure']['info']['trader']);?></figcaption>
                        </figure>
                        <figure>
                            <img src="<?php echo constant("ROUTE_IMG") . 'warrior icon.png'; ?>" title="warrior"/>
                            <figcaption><?php echo ucfirst($this->data['adventure']['current_adventure']['info']['warrior']);?></figcaption>
                        </figure>
                        <div id="status">
                        <?php
                            $test1 = in_array('none', array($this->data['adventure']['current_adventure']['info']['farmer'],
                                 $this->data['adventure']['current_adventure']['info']['miner'],
                                 $this->data['adventure']['current_adventure']['info']['trader'],
                                 $this->data['adventure']['current_adventure']['info']['warrior']));
                            $test2 = in_array(0, $this->data['adventure']['current_adventure']['requirements']);
                            $test3 = $this->data['adventure']['current_adventure']['info']['adventure_status'] == 0;
                            if($test1 == true && $test3 !== false): ?>
                            <p> Adventure status: more players needed </p>
                            <?php endif;?>
                            <?php if($test2 == true && $test3 == true): ?>
                            <p> Adventure status: awaiting providing </p>
                            <?php elseif($test1 == false && $test3 == true): ?>
                            <p> Adventure status: ready to start! </p>
                            <?php endif;?>
                            <?php if($test1 != true && $test2 != true && $test1 != true): ?>
                            <p> Adventure status: underway! </p>
                            <?php endif;?>
                        </div>
                </div>
            <?php endif;?>
        </div>
        <div class="sidebar_tab" id="tab_2">
            <table>
                <caption> Countdowns </caption>
                <thead>
                    <tr>
                        <td> Profiency </td>
                        <td> What </td>
                    </tr>
                </thead>
                <tr>
                    <td> Farmer </td>
                    <td><p>Towhar: <?php echo $this->data['countdowns']['farmer'][0];?></p>
                        <p>Cruendo: <?php echo $this->data['countdowns']['farmer'][1];?></p>
                    </td>
                </tr>
                <tr>
                    <td> Miner </td>
                    <td><p>Golbak: <?php echo $this->data['countdowns']['miner'][0];?></p>
                        <p>Snerpiir: <?php echo $this->data['countdowns']['miner'][1];?></p>
                    </td>
                </tr>
                <tr>
                    <td> Trader </td>
                    <td><?php echo 'Assignment: ' , $this->data['countdowns']['trader'];?></td>
                </tr>
                <tr>
                    <td> Warrior </td>
                    <td><p><?php echo 'Armymission: ' , $this->data['countdowns']['warrior']['mission_countdown'];?></p>
                        <p><?php echo 'Warrior(s) finished: ' , $this->data['countdowns']['warrior']['finished'];?></p>
                        <p><?php echo 'Warrior(s) training: ' , $this->data['countdowns']['warrior']['training'];?></p>
                        <p><?php echo 'Warrior(s) on mission: ' , $this->data['countdowns']['warrior']['mission'];?></p>
                        <p><?php echo 'Warrior(s) idle: ' , $this->data['countdowns']['warrior']['idle'];?></p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="sidebar_tab" id="tab_3">
            <table>
                <caption> Diplomacy </caption>
                <thead>
                    <tr>
                        <td> Location </td>
                        <td> Diplomacy </td>
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
        <div class="sidebar_tab" id="tab_4">
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
                        <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['warrior']['level'];?></figcaption>
                    </figure>
                    <span class="skill_tooltip"></span>
                    <!-- Span to display xp when gained -->
                    <span></span>
                </div>
            </div>
        </div>
    </div>
