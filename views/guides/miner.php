<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Profiencies </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("general", "profiency advantage", "minerals", "items");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header" id="general"> General </h5>
    <p>
        Miner profiency is about mining minerals from mines and/or smithing items from minerals.
    </p>
    <h5 class="p_header" id="profiency_advantage"> Profiency Advantage </h5>
    <p id="profiency_advantage">
        Check out <a href="/gameguide/profiencies"> profiencies</a> for advantages.
    </p>
    <h5 class="p_header" id="minerals"> Minerals </h5>
        <p>
        <table>
            <thead>
                <tr>
                    <td> Level </td>
                    <td> Mineral </td>
                    <td> Bar </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("1", "iron");
            $list_info[] = array("5", "steel");
            $list_info[] = array("10", "clay");
            $list_info[] = array("15", "adron");
            $list_info[] = array("40", "gargonite");
            $list_info[] = array("40", "yeqdon");
            $list_info[] = array("40", "frajrite");
            $list_info[] = array("40", "wujkin");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . ' ore.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][1]) . ' ore';?></figcaption>
                </figure></td>
                <td><figure>
                    <?php if($i === 2): ?>
                        <img src="<?php echo '../' . constant('ROUTE_IMG') . 'brick.png';?>" />
                        <figcaption><?php echo 'Brick';?></figcaption>
                    <?php else:?>
                        <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][1] . ' bar.png';?>" />
                        <figcaption><?php echo ucfirst($list_info[$i][1]) . ' bar';?></figcaption>
                    <?php endif;?>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
        </p>
</div>