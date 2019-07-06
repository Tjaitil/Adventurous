<?php
    foreach($data['stockpile'] as $key): ?>
        <div class="stockpile_item">
            <div class="stockpile_buttons">
                <button onclick="withdraw(this, 1);"> 1 </button>
                <button onclick="withdraw(this, 5);"> 5 </button>
                <button onclick="withdraw(this, 'x');"> x </button>
                <button onclick="withdraw(this, 'all');"> All </button>
            </div>
            <figure><img src="<?php echo constant('ROUTE_IMG') . $key['item'] . '.jpg';?>" height="50px" witdh="50px" />
                <figcaption><?php echo $key['amount'];?> x <?php echo ucwords(str_replace("_", " ",$key['item'])); ?></figcaption>
            </figure>
            
        </div>
    <?php endforeach; ?>
    <p><?php echo count($data['stockpile']), " / 60"?></p>

