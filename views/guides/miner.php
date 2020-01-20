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
    <p>
        The advantages of having miner as profiency is as follows:
        <ul>
            <li> Getting above level 30 </li>
            <li> Acess to mining/smithing frajrite and yeqdon </li>
            <li> Reduced prices at smithy </li>
            <li> Chance to get Wujkin minerals from adventures </li>
        </ul>
    </p>
    <h5 class="p_header" id="minerals"> Minerals </h5>
        <p>
        <table>
            <thead>
                <tr>
                    <td> Level </td>
                    <td> Mineral </td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("1", "iron ore");
            $list_info[] = array("5", "steel ore");
            $list_info[] = array("10", "clay ore");
            $list_info[] = array("15", "adron ore");
            $list_info[] = array("40", "gargonite ore");
            $list_info[] = array("40", "yeqdon ore");
            $list_info[] = array("40", "frajrite ore");
            $list_info[] = array("40", "wujkin ore ");
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><?php echo $list_info[$i][0];?></td>
                <td><figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $list_info[$i][1] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][1]);?></figcaption>
                </figure></td>
            </tr>
            <?php endfor;?>
        </table>
        </p>
</div>