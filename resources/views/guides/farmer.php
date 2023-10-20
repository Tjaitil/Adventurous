<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Profiencies </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("general", "profiency_advantage","level_overview", "crops", "seeds", "effiency");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> About </h5>
    <p> Farmer is a profiency about producing food</p>
    <h5 class="p_header"> About </h5>
    <p id="profiency_advantage">
        Check out <a href="/gameguide/profiencies"> profiencies</a> for advantages.
    </p>
    <h5 class="p_header"> Level Overview </h5>
    <p id="level_overview">
        <table>
            <thead>
                <tr>
                    <td>Level</td>
                    <td>What</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("1", "potato");
            $list_info[] = array("3", "tomato");
            $list_info[] = array("5", "corn");
            $list_info[] = array("10", "carrot");
            $list_info[] = array("15", "sugar");
            $list_info[] = array("20", "cabbage");
            $list_info[] = array("25", "spices");
            $list_info[] = array("30", "apple");
            $list_info[] = array("35", "oranges");
            $list_info[] = array("40", "watermelon");
            $list_info[] = array("50", "wheat");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][1]);?></figcaption>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
    <h5 class="p_header" id="crops"> Crops </h5>
    <p>
        <span>Different types of crops to grow in Towhar:</span>
        <table>
            <thead>
                <tr>
                    <td> Level </td>
                    <td> Seed image </td>
                    <td> Full grown image </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("1", "potato");
            $list_info[] = array("3", "tomato");
            $list_info[] = array("5", "corn");
            $list_info[] = array("10", "carrot");
            $list_info[] = array("20", "cabbage");
            $list_info[] = array("50", "wheat");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . ' seed.png';?>" />
                    <figcaption><?php echo ucwords($list_info[$i][1]) . ' seed';?></figcaption>
                </figure></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . '.png';?>" />
                    <figcaption><?php echo ucwords($list_info[$i][1]);?></figcaption>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
        Different types of crops to grow in ..:
        <table>
            <thead>
                <tr>
                    <td> Level </td>
                    <td> Seed Img </td>
                    <td> Full grown </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("15", "sugar cane");
            $list_info[] = array("25", "spices");
            $list_info[] = array("30", "apple");
            $list_info[] = array("35", "oranges");
            $list_info[] = array("40", "watermelon");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . ' seed.png';?>" />
                    <figcaption><?php echo ucwords($list_info[$i][1]) . ' seed';?></figcaption>
                </figure></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . '.png';?>" />
                    <figcaption><?php echo ucwords($list_info[$i][1]);?></figcaption>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
    <h5 class="p_header" id="seeds">Seeds</h5>
    <p>
        Seeds can be obtained from crops in farm
    </p>
    <h5 class="p_header" id="effiency"> Effiency </h5>
    <p>
        <span> Workers start with level 1 effiency level. Upgrades can be done in citycentre. </span>
        <table>
            <thead>
                <tr>
                    <td> Effiency Level: </td>
                    <td> Needed skill level </td>
                    <td> Cost </td>
                </tr>
            </thead>
            <tr>
                <td> 2 </td>
                <td> 5 </td>
                <td><span><?php $effiency_cost = 150;
                echo 2 * $effiency_cost;?></span>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 3 </td>
                <td> 13</td>
                <td><?php echo 3 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 4 </td>
                <td> 18 </td>
                <td><?php echo 4 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 5 </td>
                <td> 22 </td>
                <td><?php echo 5 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 6 </td>
                <td> 26 </td>
                <td><?php echo 6 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 7 </td>
                <td> 31 </td>
                <td><?php echo 7 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 8 </td>
                <td> 36 </td>
                <td><?php echo 8 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 9 </td>
                <td> 44 </td>
                <td><?php echo 9 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
            <tr>
                <td> 10 </td>
                <td> 50 </td>
                <td><?php echo 10 * $effiency_cost;?>
                <img src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
            </tr>
        </table>
    </p>
</div>