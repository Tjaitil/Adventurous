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
        <p><?php echo ucfirst($_SESSION['gamedata']['username']);?></p>
        <p class="mt-1 mb-1"><?php echo ucfirst($_SESSION['gamedata']['profiency']);?></p>
        <button class="sidebar_button">Adventure 
            <?php echo ($this->data['adventure']['adventure_data']['notification'] == 1) ? '(!)' : ""?></button>
        <button class="sidebar_button">Countdowns</button>
        <button class="sidebar_button">Diplomacy</button>
        <button class="sidebar_button">Skills</button>
        <div class="sidebar_tab" id="tab_1">
            <?php if(intval($this->data['adventure']['adventure_id']) != 0): ?>
                <div id="people" class="mt-1">
                    <figure>
                        <img src="<?php echo constant("ROUTE_IMG") . 'farmer icon.png'; ?>" title="farmer"/>
                        <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['farmer']);?></figcaption>
                    </figure>
                    <figure>
                        <img src="<?php echo constant("ROUTE_IMG") . 'miner icon.png'; ?>" title="miner"/>
                        <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['miner']);?></figcaption>
                    </figure>
                    <figure>
                        <img src="<?php echo constant("ROUTE_IMG") . 'trader icon.png'; ?>" title="trader"/>
                        <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['trader']);?></figcaption>
                    </figure>
                    <figure>
                        <img src="<?php echo constant("ROUTE_IMG") . 'warrior icon.png'; ?>" title="warrior"/>
                        <figcaption class="mt-05"><?php echo ucfirst($this->data['adventure']['info']['warrior']);?></figcaption>
                    </figure>
                </div>
                <div id="status" class="mt-1">
                    <?php get_template('adventure_status', $this->data['adventure']);?>
                </div>
                <div id="requirements" class="mt-1">
                    <table class="middle-align">
                        <thead>
                            <tr>
                                <td><b>Role</b></td>
                                <td><b>Requirement</b></td>
                                <td><b>Provided</b></td>
                            </tr>
                        </thead>
                        <?php get_template('requirements', $this->data['adventure']['requirements']); ?>
                    </table>
                </div>
                <?php else: ?>
                <span> No current adventure! </span>
            <?php endif;?>
            <div id="adventure-invites">
            <?php get_template('adventure_requests', 
                    array("requests" => $this->data['adventure']['requests'], "invites" => $this->data['adventure']['invites']), 
                    false, array("site" => "adventures"));?>
            </div>
        </div>
        <div class="sidebar_tab lightTextColor" id="tab_2">
            <?php get_template('countdown', $this->data['countdowns']);?>
        </div>
        <div class="sidebar_tab" id="tab_3">
            <?php
                function fetchDiplomacyClass($diplomacy) {
                    if($diplomacy > 1) {
                        return 'class="positiveDiplomacy"';
                    } else if($diplomacy < 1) {
                        return 'class="negativeDiplomacy"';
                    } else {
                        return; 
                    }
                }
            ?>
            <div class="mt-1">
                <img src="<?php echo constant('ROUTE_IMG') . 'diplomacy icon.png'?>">
            </div>
            <table class="lightTextColor middle-align">
                <thead>
                    <tr>
                        <td> Location </td>
                        <td> Diplomacy </td>
                    </tr>
                </thead>
                <tr>
                    <td> Hirtam </td>
                    <td <?php echo fetchDiplomacyClass($this->data['diplomacy']['hirtam']);?>>
                        <?php echo $this->data['diplomacy']['hirtam'];?>
                    </td>
                </tr>
                <tr>
                    <td> Pvitul </td>
                    <td <?php echo fetchDiplomacyClass($this->data['diplomacy']['pvitul']);?>>
                        <?php echo $this->data['diplomacy']['pvitul'];?>
                    </td>
                </tr>
                <tr>
                    <td> Khanz </td>
                    <td <?php echo fetchDiplomacyClass($this->data['diplomacy']['khanz']);?>>
                        <?php echo $this->data['diplomacy']['khanz'];?>
                    </td>
                </tr>
                <tr>
                    <td> Ter </td>
                    <td <?php echo fetchDiplomacyClass($this->data['diplomacy']['ter']);?>>
                        <?php echo $this->data['diplomacy']['ter'];?>
                    </td>
                </tr>
                <tr>
                    <td> Fansal Plains </td>
                    <td <?php echo fetchDiplomacyClass($this->data['diplomacy']['fansalplains']);?>>
                        <?php echo $this->data['diplomacy']['fansalplains'];?>
                    </td>
                </tr>
                </tr>
            </table>
        </div>
        <div class="sidebar_tab darkTextColor" id="tab_4">
            <div id="skills" class="mt-1">
                <div onclick="get_xp('adventurer')" class="darkTextColor">
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'adventurer icon.png';?>" />
                        <figcaption><?php echo $_SESSION['gamedata']['adventurer'];?></figcaption>
                    </figure>
                    <span class="skill_tooltip"></span>
                    <span></span>
                </div>
                <div onclick="get_xp('farmer')" class="darkTextColor">
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png';?>" />
                        <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['farmer']['level'];?></figcaption>
                    </figure>
                    <span class="skill_tooltip"></span>
                    <!-- Span to display xp when gained -->
                    <span></span>
                </div>
                <div onclick="get_xp('miner')" class="darkTextColor">
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png';?>" />
                        <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['miner']['level'];?></figcaption>
                    </figure>
                    <span class="skill_tooltip"></span>
                    <!-- Span to display xp when gained -->
                    <span></span>
                </div>
                <div onclick="get_xp('trader')" class="darkTextColor">
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png';?>" />
                        <figcaption class="skill_level"><?php echo $_SESSION['gamedata']['trader']['level'];?></figcaption>
                    </figure>
                    <span class="skill_tooltip"></span>
                    <!-- Span to display xp when gained -->
                    <span></span>
                </div>
                <div onclick="get_xp('warrior')" class="darkTextColor">
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
