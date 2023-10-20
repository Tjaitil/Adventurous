<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Trader </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("general", "profiency advantage", "assigment types", "cart types");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> General </h5>
    <p></p>
    <h5 class="p_header" id="profiency_advantage"> Profiency Advantage </h5>
    <p id="profiency_advantage">
        Check out <a href="/gameguide/profiencies"> profiencies</a> for advantages.
    </p>
    <h5 class="p_header" id="assignement_types"> Different types of assignments </h5>
    <p>
        <table>
            <thead>
                <tr>
                    <td>Level</td>
                    <td>What</td>
                    <td>Experience</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array(1, "small trade", 50);
            $list_info[] = array(1, "favor", 90);
            $list_info[] = array(15, "medium trade", 100);
            $list_info[] = array(31, "large trade", 250);
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <figcaption><?php echo ucfirst($list_info[$i][1]);?></figcaption>
                </figure></td>
                <td><?php echo $list_info[$i][2];?></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
    <h5 class="p_header" id="cart_types"> Cart Types </h5>
    <p>
        <table>
            <thead>
                <tr>
                    <td>Level</td>
                    <td>What</td>
                    <td>Wood</td>
                    <td>Gold</td>
                    <td>Capasity</td>
                    <td>Mineral amount</td>
                    <td>Cart amount</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array(1, "iron cart", "birch", 300, 20, 3, 12);
            $list_info[] = array(10, "steel cart", "oak", 300, 50, 5, 12);
            $list_info[] = array(25, "yeqdon cart", "yew", 300, 100, 5, 12);
            $list_info[] = array(35, "frajrite cart", "yew", 300, 200, 5, 12,);
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][1]);?></figcaption>
                </figure></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][2] . ' logs' . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][2]);?></figcaption>
                </figure></td>
                <td><?php echo ucfirst($list_info[$i][3]);?>
                <img class="gold" src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                <td><?php echo ucfirst($list_info[$i][4]);?></td>
                <td><?php echo ucfirst($list_info[$i][5]);?></td>
                <td><?php echo ucfirst($list_info[$i][6]);?></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
</div>