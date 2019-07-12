
    <?php
        foreach($data['shop'] as $key): ?>   
            <div class="item">
                <figure><img src="/public/images/gold.jpg" height="50px" witdh="50px" />
                    <figcaption><? echo $key[$data['city']], ' ', 'x', ' ', $key['item'], ' ', '(', $key['cost'],')';?>
                    </figcaption>
                </figure>
                <button onclick="buyItem('<?php echo $key['item'] ?>', 1);"> Buy </button>
            </div>
    <?endforeach;?>