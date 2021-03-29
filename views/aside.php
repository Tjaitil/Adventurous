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
        <button onclick="sidebar.toggleSidebar();" id="sidebar_button_toggle" class="sidebar_button"> << </button>
        <p>Current profiency: <?php echo ucfirst($_SESSION['gamedata']['profiency']);?></p>
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
                        <div id="requirements">
                        <table>
                            <thead>
                                <tr>
                                    <td> Role: </td>
                                    <td> Requirement: </td>
                                    <td> Provided: </td>
                                </tr>
                            </thead>
                            <?php get_template('requirements', $this->data['adventure']['current_adventure']['requirements']); ?>
                        </table>
                    </div>
                </div>
            <?php endif;?>
        </div>
        <div class="sidebar_tab" id="tab_2">
            <?php get_template('countdown', $this->data['countdowns']);?>
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
                        <figcaption id="ad_tooltip"><?php echo $_SESSION['gamedata']['adventurer'];?></figcaption>
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
