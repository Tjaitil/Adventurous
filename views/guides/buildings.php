<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Buildings </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("introduction", "different buildings");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header" id="introduction"> Introduction </h5>
    <p>
        Buildings can be found through out all of adventurous. All cities will not have the same buildings but every city will at least
        have merchant, travel bureau and city centre. The different buildings provide different actions to the player. Note that
        buildings may seems similar in style but the difference is often on the door. Some buildings listed below are the only ones that
        can be entered by the player.
    </p>
    <h5 class="p_header" id="different_buildings"> Different buildings </h5>
    <p>
        <table>
            <thead>
                <tr>
                    <td>Type</td>
                    <td>Description</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array('city centre', "Buy permits, upgrade workers, change effiency");
            $list_info[] = array('smithy', "Make armour and weapons from minerals");
            $list_info[] = array('archery shop', "Make bows and arrows");
            $list_info[] = array('mine', "Mine minerals");
            $list_info[] = array('merchant', "Buy and sell your goods");
            $list_info[] = array('crops', "Grow crops");
            $list_info[] = array('adventure base', "Go on adventures");
            $list_info[] = array('bakery', "Make food from ingredients");
            $list_info[] = array('workforce lodge', "Actions related to workforce");
            $list_info[] = array('army camp', "Train warriors, armymissions");
            $list_info[] = array('travel bureau', "Buy carts");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><figure>
                    <img class="building_img" src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][0] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][0]);?></figcaption>
                </figure></td>
                <td><?php echo $list_info[$i][1];?></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
</div>