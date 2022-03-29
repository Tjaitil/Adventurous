<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Artefacts </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("about", "aquiring", "types");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header" id="about"> About </h5>
    <p>
        Artefact are an item which gives you special bonuses. Each artefact has 10 charges and after those charges are used up the
        artefact disappears
    </p>
    <h5 class="p_header" id="aquiring"> Aquiring </h5>
    <p>
        Artefacts are aquired by speaking to Harfen <img src="<?php echo '../' .constant('ROUTE_IMG') . 'harfen.png'?>" /> 
        at any given location. You must bring all the five different crystals you get from adventures in different locations
        <?php $crystals = array("pvitul", "hirtam", "khanz", "fansal-plains", "ter");
        for($i = 0; $i < count($crystals); $i++): ?>
            <figure>
                <img src="<?php echo '../' .constant('ROUTE_IMG') . $crystals[$i] . ' crystal.png';?>" 
                    alt="<?php echo ucwords($crystals[$i] . ' crystal');?>">
                <figcaption><?php echo ucwords($crystals[$i] . ' crystal');?></figcaption>
            </figure>

        <?php endfor;?>
    </p>
    <h5 class="p_header" id="types"> Types </h5>
    <p>
        <table>
            <thead>
                <tr>
                    <td>Artefact</td>
                    <td>Bonus</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array("prosepector", "lorem ipsum");
            $list_info[] = array("rewardist", "lorem ipsum");
            $list_info[] = array("healer", "lorem ipsum");
            $list_info[] = array("harvester", "lorem ipsum");
            $list_info[] = array("fighter", "lorem ipsum");

            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][0] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][0]);?></figcaption>
                </figure></td>
                <td><?php echo $list_info[$i][1];?></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
</div>