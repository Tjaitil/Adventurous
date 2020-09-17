<?php
    function amount($value) {
        switch(true) {
            case $value >= 1000:
                $var = $value / 100;
                $var = (int)$var;
                return ($var / 10) . 'k';
                break;
            default:
                return $value;
                break;
        }
    }
    function url($url = false) {
        if($url == false) {
            $url = $_SERVER['REQUEST_URI'];
            $url = ltrim($url, '/');
            $url = explode("/", $url);
            $url = $url[0];
        }
        else {
            $url = $url;
        } ?>
        <button id="inv_toggle_button"> INV </button>
        <p> Inventory <?php echo '(' . count($_SESSION['gamedata']['inventory']) . '/' . '18' . ')';?></p>
        <div id="item_tooltip">
            <ul>
                <li></li>
            </ul>
        </div>
<?php
        switch($url):
        case 'stockpile':
        foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span class="item_amount"><?php echo amount($key['amount']);?></span>
            </div>
        <?php endforeach; break;?>
        <?php default: ?>
        <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span class="item_amount"><?php echo amount($key['amount']);?></span>
            </div>
            <?php endforeach;?>
        <?php break;
        endswitch;
    }?>