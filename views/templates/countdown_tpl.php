<?php 
    function checkStatus($data_array, $profiency, $workforce_total) {
        if($profiency === 'farmer') {
            $img_src_ending = '.png';
            $type = 'crop_type';
        } else {
            $img_src_ending = ($profiency === 'farmer') ? '.png' : ' ore.png';
            $type = 'mining_type';
        }
        ?> 
        <div class="countdown-flex-container"> 
        <?php
        if($data_array['status'] === 'nothing happening'): ?>
            <p> Nothing happening </p>
        <?php else: ?>
            <div>
                <p>Used workforce <?php echo $data_array['workforce'] . '/' .
                        $workforce_total;?></p>
                <p><?php echo $data_array['status'];?></p>
            </div>
            <div>
                <figure class="item">
                    <img src="<?php echo constant('ROUTE_IMG') . $data_array[$type]. $img_src_ending;?>" />
                    <figcaption><?php echo ucwords($data_array[$type]);?></figcaption>
                </figure>
            </div>
        <?php endif; ?>
        </div>
        <?php
    }
?>
<table class="lightTextColor">
    <caption> Profiency countdowns </caption>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'farmer icon.png';?>" />
            <p class="mt-1">Towhar</p>
            <?php checkStatus($data['farmer']['towhar'], 'farmer', $data['farmer']['workforce']['workforce_total']);?>
            <p class="mt-1">Krasnur</p>
            <?php checkStatus($data['farmer']['krasnur'], 'farmer', $data['farmer']['workforce']['workforce_total']);?>
        </td>
    </tr>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'miner icon.png';?>" />
            <div>
                <p class="mt-1">Golbak</p>
                <?php checkStatus($data['miner']['golbak'], 'miner', $data['miner']['workforce']['workforce_total']);?>
            </div>
            <div>
                <p class="mt-1">Snerpiir</p>
                <?php checkStatus($data['miner']['snerpiir'], 'miner', $data['miner']['workforce']['workforce_total']);?>
            </div>
        </td>
    </tr>
    <tr>
        <td> 
            <img src="<?php echo constant('ROUTE_IMG') . 'trader icon.png';?>" />
            <?php if(intval($data['trader']['assignment_id']) === 0): ?>
                <?php echo 'none';?>
            <?php else: ?>
                <div id="traderAssignment_current">
                    <div class="traderAssignment_fullColumn fullLengthColumn">
                        <figure class="item">
                            <img src="<?php echo constant('ROUTE_IMG') . strtolower($data['trader']['cargo']) . '.png';?>" />
                                <figcaption><?php echo ucwords($data['trader']['cargo']);?></figcaption>
                        </figure>
                    </div>
                    <div>
                        <?php echo ucwords($data['trader']['base']) . ' -> ' .  ucwords($data['trader']['destination']);?>
                    </div>
                    <div>
                        <span>
                            Cart Capasity: <?php echo $data['trader']['cart_amount'] , '/', $data['trader']['capasity'];?>
                        </span>
                    </div>
                </div>
            <?php endif;?>
        </td>
    </tr>
    <tr>
        <td>
            <img src="<?php echo constant('ROUTE_IMG') . 'warrior icon.png';?>" />
            <p><?php echo $data['warrior']['armymission']; ?></p>
            <p><?php echo 'Warrior(s) finished training: ' , $data['warrior']['finished'];?></p>
            <p><?php echo 'Warrior(s) training: ' , $data['warrior']['training'];?></p>
            <p><?php echo 'Warrior(s) on mission: ' , $data['warrior']['mission'];?></p>
            <p><?php echo 'Warrior(s) idle: ' , $data['warrior']['idle'];?></p>
        </td>
    </tr>
</table>