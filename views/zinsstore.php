        zinsstore.css|zinsstore.js|
        <h3 class="page_title"> Zins Store </h3>
        <p class="help">
            Trade loot from daqloons
        </p>
        <div id="zinsstore">
            <div class="item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . 'daqloon horns.png'?>">
                    <figcaption> Daqloon horns </figcaption>
                </figure>
                <p>1500 x <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></p>
            </div>
            <div class="item">
                <figure>
                    <img src="<?php echo constant('ROUTE_IMG') . 'daqloon scale.png'?>">
                    <figcaption> Daqloon scale </figcaption>
                </figure>
                <p>250 x <img class="gold" src="<?php echo constant('ROUTE_IMG') . 'gold.png';?>" /></p>
            </div>
            </br>
            <input type="number" name="zinsstore_amount" id="zinsstore_item_amount" min="0"/></br>
            <button id="zinsstore_trade"> Trade </button>
        </div>