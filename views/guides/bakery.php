<a href="/gameguide"> Back to main page</a>
<h3 class="guide_title"> Bakery </h3>
<div id="content_table">
    <ol>
    <?php $contents = array("Bakery", "Cost");
    for($i = 0; $i < count($contents); $i++): ?>
        <li><a href="#<?php echo $contents[$i];?>"><?php echo $contents[$i];?></a></li>
    <?php endfor;?>
    </ol>
</div>
<div id="content">
    <h5 class="p_header"> </h5>
    <p id="">
        <table>
            <thead>
                <tr>
                    <td>Type</td>
                    <td>Ingredients</td>
                    <td>Cost</td>
                    <td>Heal</td>
                </tr>
            </thead>
            <?php $list_info = array();
            $list_info[] = array('cooked potato', array('potato'), 50, 8);
            $list_info[] = array('roasted tomato', array('tomato'), 0, 0);
            $list_info[] = array('cooked carrots', array('carrots'), 0, 0);
            $list_info[] = array('roasted corn', array('corn'), 0, 0);
            $list_info[] = array('cooked cabbage', array('cabbage'), 0, 0);
            $list_info[] = array('cooked beans', array('beans'), 0, 0);
            $list_info[] = array('vegetable pie', array('tAny vegetable', 'cabbage'), 0, 0);
            $list_info[] = array('bread', array('wheat', 'sugar'), 0, 0);
            $list_info[] = array('stew', array('tomato', 'beef'), 0, 0);
            $list_info[] = array('spicy chicken', array('spices', 'chicken'), 0, 0);
            $list_info[] = array('spicy pork', array('spices', 'pork'), 0, 0);
            $list_info[] = array('spicy beef', array('spices', 'beef'), 0, 0);
            $list_info[] = array('fruit salad', array('apples', 'oranges', 'watermelon'), 0, 0);
            $list_info[] = array('apple pie', array('apple', 'sugar', 'wheat'), 0, 0);
            $list_info[] = array('bass', array('raw bass'), 0, 0);
            $list_info[] = array('hornfish', array('raw hornfish'), 0, 0);
            $list_info[] = array('salmon', array('raw salmon'), 0, 0);
            $list_info[] = array('lobster', array('raw lobster'), 0, 0);
            $list_info[] = array("ent'a", array("raw ent'a"), 0, 0);
            $list_info[] = array("ent'a soup", array("raw ent'a", 'tomato'), 0, 0);
            $list_info[] = array('beef', array('raw beef'), 0, 0);
            $list_info[] = array('chicken', array('raw chicken'), 0, 0);
            $list_info[] = array('pork', array('raw pork'), 0, 0);
            for($i = 0; $i < count($list_info); $i++): ?>
            <tr>
                <td><figure>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $list_info[$i][0] . '.png';?>" />
                    <figcaption><?php echo ucfirst($list_info[$i][0]);?></figcaption>
                </figure></td>
                <td><?php foreach($list_info[$i][1] as $key => $value): ?>
                    <img src="<?php echo '../' . constant('ROUTE_IMG') . $value . '.png';?>" />
                    <figcaption><?php echo ucfirst($value);?></figcaption>
                    <?php endforeach;?>
                    </td>
                <td><?php echo ucfirst($list_info[$i][2]);?>
                <img class="gold" src="<?php echo '../'. constant('ROUTE_IMG') . 'gold.png';?>" /></td>
                <td><?php echo ucfirst($list_info[$i][3]);?></td>
            </tr>
            <?php endfor;?>
        </table>
    </p>
</div>