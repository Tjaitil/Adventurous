            merchant.css|merchant.js|
            <h1 class="page_title">Merchant</h1>
            <div id="trades">
                <p id="trades_countdown">New merchant offers in <span id="trades_countdown_time"></span></p>
                <p class="help mb-1 mt-1">Beware that diplomacy relation to places can affect prices. Difference will be displayed in
                parenthesis. Trading in adventure locations will affect diplomacy relation</p>
                <div id="merchant-offer-container" class="div_content div_content_dark">
                    <div id="merchant-offer-list" class=".pb-05">
                        <?php get_template('merchantOffers', $this->data['offers'], true); ?>
                    </div>
                    <div id="merchant-offer-selected" class="div_content_dark">
                        <div id="do_trade" class="div_content_dark">
                            <div id="selected_trade">

                            </div>
                            <p></p>
                            <p id="trade_price"><span></span>
                                <img class="gold" src="<?php echo constant("ROUTE_IMG") . 'gold.png'; ?>" />
                            </p>
                            <label for="amount"> Amount </label></br>
                            <input type="number" id="amount" name="amount" min="0" /></br>
                            <button id="trade_button"> Trade </button>
                        </div>
                    </div>
                </div>
                <p id="trade_info" class="mb-1 mt-1">
                    Click on shop item to buy or click on inventory item to sell item. The store prices are listed as buy price / sell price.
                    The merchant will only accept trading on items it is already selling. Head to Fagna to sell all items.
                </p>
            </div>
            <div id="trader_assignments">
                <div id="traderAssignment_current" class="content_div mb-2">
                    <p class="traderAssignment_fullColumn"> Current trader assignment details </p>
                    <?php get_template('traderAssignment', $this->data['trader_data'], true); ?>
                </div>
                <p id="trader_assignments_countdown">New trader assignments in <span id="trader_assignments_countdown_time"></span></p>
                <h4>Select your assignment below. Greyed out assignments are locked</h4>
                <div id="trader_assignments_container">
                    <?php get_template('assignment', $this->data['trader_assignments'], true); ?>
                </div>
                <button type="button" id="start_trader_assignment" class="mt-1 mb-1">Do Assigment</button>
            </div>