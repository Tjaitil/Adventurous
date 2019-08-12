<?php
    function amount($value) {
        switch(true) {
            case $value >= 1000:
                return round($value / 1000, 1) . 'k' ;
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
            <div class="inventory_item">
                <div class="inventory_buttons">
                    <!--
                    --><button
                        onclick="insert(this, 1);"> 1 </button><!--
                    --><button
                        onclick="insert(this, 5);"> 5 </button><!--
                    --><button
                        onclick="insert(this, 'x');"> x </button><!--
                    --><button
                        onclick="insert(this, 'all');"> All </button><!--
                    -->
                </div>
                <figure onclick="show_title(this, true);">
                <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" />
                <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span id="item_amount"><? echo amount($key['amount']);?></span>
            </div>
        <?php endforeach; break; ?>
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
            <? endforeach; ?>
        
        <? break; ?>
        <?php case 'market': ?>
        <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">    
                <figure onclick="select_i(this);"><img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" />
                    <figcaption><span><? echo $key['amount'];?></span> x <?php echo ucwords($key['item']); ?></figcaption>
                </figure>          
            </div>
            <? endforeach; break;?>
        <?php case 'smithy':
              case 'bakery':
              case 'crops':?>
            <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">    
                <figure><img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>"  />
                    <figcaption><? echo $key['amount'];?> x <?php echo ucwords($key['item']); ?></figcaption>
                </figure>          
            </div>
            <? endforeach; ?>
        <? break;
        default: ?>
        <?php foreach($_SESSION['gamedata']['inventory'] as $key): ?>
            <div class="inventory_item">
                <figure onclick="show_title(this, false);">
                    <img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" />
                    <figcaption class="tooltip"><?php echo ucwords($key['item']); ?></figcaption>
                </figure>
                <span id="item_amount"><? echo amount($key['amount']);?></span>
            </div>
            <? endforeach; ?>
        <? break;
        endswitch;
    }?>