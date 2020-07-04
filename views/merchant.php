            merchant.css|merchant.js%trader.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="trades">
                <div>
                <?php get_template('merchantStock', $data); ?>
                </div>
                <div id="do_trade">
                    <div id="selected_trade">
                        
                    </div>
                    <label for="amount"> Amount </label></br>
                    <input type="number" id="amount" name="amount" /></br>
                    <label for="bond"> Use bond? </label></br>
                    <input type="checkbox" id="bond" name="bond" /></br>
                    <button> Trade </button>
                </div>
            </div>
            <div id="trader">
                <table>
                    <thead>
                        <tr>
                            <th> Delivery </th>
                            <th> Where </th>
                            <th> Cargo </th>
                            <th> Cargo Amount </th>
                            <th> Time </th>
                            <th> Assignment Type </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tr>
                        <?php get_template('assignment', $data['trader_assignments']);?>
                    </tr>
                </table>
                <div id="assignment">
                    <p>Cart Capasity: <?php echo $data['trader_data']['cart_amount'] , '/', $data['trader_data']['capasity'];?></p>
                    <p>Current assignment: <?php echo $data['trader_data']['assignment'];?></p>
                    <div id="pick_up">
                        <button onclick="pickUp();">Pick up items</button>
                    </div>
                    <div id="deliver">
                        <button onclick="deliver();">Deliver</button>
                    </div>
                </div>