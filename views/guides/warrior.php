<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Warrior </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("general", "profiency advantage", "warrior", "warrior equipment", "armymission");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo str_replace(" ", "_", $contents[$i]);?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> General </h5>
    <p id="general">
        
    </p>
    <h5 class="p_header"> Profiency Advantage </h5>
    <p id="profiency_advantage">
        Check out <a href="/gameguide/profiencies"> profiencies</a> for advantages.
    </p>
    <h5 class="p_header"> Warrior</h5>
    <p id="warrior">
        Warriors can be recruited from taverns across cities.
        There are two types of warriors, melee and ranged.
        
        The available skills to train are as follows:
        <table>
            <thead>
                <tr>
                    <td>Skill</td>
                    <td>Explanation</td>
                </tr>
            </thead>
            <tr>
                <td>Stamina <img src="<?php echo '../' . constant('ROUTE_IMG') . 'stamina icon.png';?>" /></td>
                <td>During combat your warriors will be losing energy and become more exhausted. Stamina level will help your warriors
                    to fight longer</td>
            </tr>
            <tr>
                <td>Technique <img src="<?php echo '../' . constant('ROUTE_IMG') . 'technique icon.png';?>" /></td>
                <td>This skill is about trying to dodge attacks. The higher level gives a bigger chance to dodge the attack.
                    If the warrior is wielding a shield the chance is greatly increased</td>
            </tr>
            <tr>
                <td>Precision <img src="<?php echo '../' . constant('ROUTE_IMG') . 'precision icon.png';?>" /></td>
                <td>This skill helps ranged warriors to have stronger hits. NOTE! only for ranged warriors</td>
            </tr>
            <tr>
                <td>Strength <img src="<?php echo '../' . constant('ROUTE_IMG') . 'strength icon.png';?>" /></td>
                <td>This skill helps melee warriors to have stronger hits.  NOTE! only for melee warriors</td>
            </tr>
        </table>
        The warriors status has implications on what actions are available for your warrior
        <table>
            
        </table>
    </p>
    <h5 class="p_header" id="warrior_equipment"> Warrior equipment </h5>
    <p id="item_levels">
        The level required to wear armor type is a total level combined of different skills. For ranged it is combined of stamina,
        precision and technique and for melee it is combined of stamina, strength and technique.
        <table>
            <thead>
                <tr>
                    <td>Warrior level total required</td>
                    <td>Type of equipment</td>
                    <td>Weapons tokens required <img src="<?php echo '../' . constant('ROUTE_IMG') . 'weapon tokens.png';?>"/></td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("3", "iron", 0);
            $list_info[] = array("9", "steel", 0);
            $list_info[] = array("15", "gargonite", 0);
            $list_info[] = array("25", "adron", 0);
            $list_info[] = array("32", "yeqdon", 0);
            $list_info[] = array("40", "frajrite", 125);
            $list_info[] = array("42", "wujkin", 160);
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . ' platebody.png';?>" />
                    <figcaption><?php echo ucwords($list_info[$i][1]);?><?php echo (in_array($list_info[$i][1], array('frajrite', 'wujkin'))) ?
                                                                           ' *' : '';?></figcaption>
                </figure></td>
                <td><?php echo $list_info[$i][2];?></td>
            </tr>
            <?php endfor;?>
        </table>
        * Frajrite and Wujkin armor requires that you unlock by collecting weapon tokens from adventures. To receive weapon tokens you need to
        complete adventure as warrior profiency
    </p>
    <h5 class="p_header"> Armymission </h5>
    <p id="armymission">
        Armymission have different difficulty and also a level required to do them
        <table>
            <thead>
                <tr>
                    <td> Difficulty </td>
                    <td> Level required </td>
                    <td> Experience gained </td>
                    <td> Warrior experience </td>
                </tr>
            </thead>
            <tr>
                <td>Easy</td>
                <td>3</td>
                <td>100</td>
                <td>30 in each skill*</td>
            </tr>
            <tr>
                <td>Medium</td>
                <td>15</td>
                <td>297</td>
                <td>50 in each skill*</td>
            </tr>
            <tr>
                <td>Hard</td>
                <td>35</td>
                <td>512</td>
                <td>100 in each skill*</td>
            </tr>
        </table>
        *Only precision xp for ranged warriors and strength xp for melee warriors
    </p>
</div>