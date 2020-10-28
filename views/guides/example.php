<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> #Page Title </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo ucwords($contents[$i]);?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> </h5>
    <p id="">
        <table>
            <thead>
                <tr>
                    <td>Level</td>
                    <td>What</td>
                </tr>
            </thead>
            <?php $list_info = array();
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
</div>