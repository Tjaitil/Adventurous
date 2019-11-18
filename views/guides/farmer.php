
<div id="content_table"></div>
<div id="content">
    <h5 class="p_header"> About </h5>
    <p> Farmer is a profiency about producing food</p>
    
    <h5 class="p_header"> Level View </h5>
    <p>
        Different types of crops to grow in Towhar:
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
                <td><?php echo $list_info[$i][1];?></td>
            </tr>
            <?php endfor;?>
            <tr>
                <td> 1 </td>
                <td> Potato </td>
            </tr>
        </table>
        Different types of crops to grow in ...:
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
                <td><?php echo $list_info[$i][1];?></td>
            </tr>
            <?php endfor;?>
            <tr>
                <td> 1 </td>
                <td> Potato </td>
            </tr>
        </table>
    </p>
</div>