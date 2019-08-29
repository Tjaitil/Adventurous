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
        }
        switch($url):
        case 'stockpile':
        foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item" onpointerdown="show_menu(this, true);">
                <figure>
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span id="item_amount"><? echo amount($key['amount']);?></span>
            </div>
        <?php endforeach; break;?>
        <?php  
            case 'armory':
            case 'adventures': ?>
            <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
                <div class="inventory_item">    
                    <figure>
                        <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" />
                        <figcaption class="tooltip"><?php echo ucwords($key['item']);?></figcaption>
                    </figure>
                    <span id="item_amount"><? echo amount($key['amount']);?></span>
                </div>
            <?php endforeach; break;?>
        <?php case 'market': ?>
        <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">    
                <figure onclick="select_i(this);"><img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" />
                    <figcaption><span><? echo $key['amount'];?></span> x <?php echo ucwords($key['item']); ?></figcaption>
                </figure>          
            </div>
            <?php endforeach; break;?>
        <?php default: ?>
        <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">
                <figure onclick="show_title(this, false);">
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.png';?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span id="item_amount"><? echo amount($key['amount']);?></span>
            </div>
            <?php endforeach;?>
        <?php break;
        endswitch;
    }?>