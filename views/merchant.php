            merchant.css|merchant.js|
            <h1 class="page_title">Merchant</h1>
            <div id="trades">
                <p id="trades_countdown">New merchant offers in <span id="trades_countdown_time"></span></p>
                <p class="help mb-1 mt-1">Beware that diplomacy relation to places can affect prices. Difference will be displayed in
                    parenthesis. Trading in adventure locations will affect diplomacy relation</p>
                <div id="merchant-offer-container" class="div_content div_content_dark">
                    <div id="merchant-offer-list" class="pb-05">
                        <?php
                        //  get_template(
                        //     'merchantOffers',
                        //     [
                        //         'location' => $this->data['location'],
                        //         'offers' => $this->data['offers'],
                        //     ],
                        //     true
                        // ); 
                        ?>
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
                <?php
                echo $this->bladeRender->run('templates.traderAssignment_tpl', ['CurrentAssignment' => $CurrentAssignment, 'Trader' => $Trader]);
                ?>
                <p id="trader_assignments_countdown">New trader assignments in <span id="trader_assignments_countdown_time"></span></p>
                <h3 class="text-lg">Select your assignment below. Greyed out assignments are locked</h3>
                <div class="mb-4">
                    <h4 class="font-bold">Assignments available in this location</h4>
                    <button type="button" id="start_trader_assignment" class="mt-1 mb-1">Do Assigment</button>
                    <div id="trader_assignments_container ">
                        <?php
                        echo $this->bladeRender->run('templates.traderAssignments_tpl', ['Assignments' => $CurrentLocationAssignments, 'current_location' => $current_location, 'trader_level' => $trader_level]);
                        ?>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold">Assignments available in other locations</h3>
                    <div id="trader_assignments_container">
                        <?php
                        echo $this->bladeRender->run('templates.traderAssignments_tpl', ['Assignments' => $OtherAssignments, 'current_location' => $current_location, 'trader_level' => $trader_level]);
                        ?>
                    </div>
                </div>