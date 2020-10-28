            merchant.css|merchant.js%trader.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="trades">
                <p id="countdown">New merchant offers in <span id="time"></span></p>
                <div >
                    <?php get_template('merchantOffers', $this->data['merchant_offers'], true); ?>
                </div>
                <div id="do_trade">
                    <div id="selected_trade">
                        
                    </div>
                    <p></p>
                    <p id="trade_price"><img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png';?>" /></p>
                    <label for="amount"> Amount </label></br>
                    <input type="number" id="amount" name="amount"  min="0"/></br>
                    <button> Trade </button>
                </div>
                <p id="trade_info">
                    Click on shop item to buy or click on inventory item to sell item. The store prices are listed as buy price / sell price.
                    The merchant will only accept trading on items it is already selling. Head to Fagna to sell all items.
                </p>
            </div>
            <div id="trader">
                <table>
                    <thead>
                        <tr>
                            <th> From </th>
                            <th> To </th>
                            <th> Cargo </th>
                            <th> Cargo Amount </th>
                            <th> Time </th>
                            <th> Assignment Type </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tr>
                        <?php get_template('assignment', $this->data['trader_assignments']);?>
                    </tr>
                </table>
                <div id="assignment">
                    <p>Cart Capasity: <?php echo $this->data['trader_data']['cart_amount'] , '/', $this->data['trader_data']['capasity'];?></p>
                    <p>Current assignment: <?php echo $this->data['trader_data']['assignment'];?></p>
                    <div id="pick_up">
                        <button onclick="pickUp();">Pick up items</button>
                    </div>
                    <div id="deliver">
                        <button onclick="deliver();">Deliver</button>
                    </div>
                </div>