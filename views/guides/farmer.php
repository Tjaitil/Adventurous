<div id="content_table">
    <ol>
    <?php $contents = array("general", "level_overview", "crops", "seeds", "effiency");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> About </h5>
    <p> Farmer is a profiency about producing food</p>
    
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
            $list_info[] = array("1", "Potato");
            $list_info[] = array("3", "Tomato");
            $list_info[] = array("5", "Corn");
            $list_info[] = array("10", "Carrots");
            $list_info[] = array("15", "Sugar");
            $list_info[] = array("20", "Cabbage");
            $list_info[] = array("25", "Spices");
            $list_info[] = array("30", "Apples");
            $list_info[] = array("35", "Oranges");
            $list_info[] = array("40", "Watermelon");
            $list_info[] = array("50", "Wheat");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $list_info[$i][1];?>" />
                    <figcaption><?php echo $list_info[$i][1];?></figcaption>
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
                    <td> What </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("1", "Potato");
            $list_info[] = array("3", "Tomato");
            $list_info[] = array("5", "Corn");
            $list_info[] = array("10", "Carrots");
            $list_info[] = array("20", "Cabbage");
            $list_info[] = array("50", "Wheat");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $list_info[$i][1];?>" />
                    <figcaption><?php echo $list_info[$i][1];?></figcaption>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
        Different types of crops to grow in ..:
        <table>
            <thead>
                <tr>
                    <td> Level </td>
                    <td> What </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("15", "Sugar");
            $list_info[] = array("25", "Spices");
            $list_info[] = array("30", "Apples");
            $list_info[] = array("35", "Oranges");
            $list_info[] = array("40", "Watermelon");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $list_info[$i][1];?>" />
                    <figcaption><?php echo $list_info[$i][1];?></figcaption>
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