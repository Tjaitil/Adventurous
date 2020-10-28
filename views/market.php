            market.css|market.js|
            <h3 class="page_title"> Market </h3>
            <div id="new_off">
                <form id="offer_form">
                    <label for="type"> Buy/Sell: </label>
                    <select id="form_select" name="type" onchange="toggleType();">
                        <option selected="selected">  </option>
                        <option value="Buy"> Buy </option>
                        <option value="Sell"> Sell </option></br>
                    </select>
                    <div id="offer_types">
                        <button>
                            Buy
                        </button>
                        <div></div>
                        <button>
                            Sell
                        </button>
                        <div></div>
                    </div>
                    <div id="form_cont">
                        <label for="type"> Offer type: </label>
                        <input type="text" name="type" readonly />
                        <div id="item">
                            <div id="selected">
                            
                            </div>
                            <input type="text" id="item_name" name="item" placeholder="Item Name" readonly />
                        </div>
                        <div id="form_inputs">
                            <div id="item_b">
                                <label for="item_srch"> Enter name: </label>
                                <input id="item_srch" type="text" /></br>
                                Select item:
                                <select id="items" onchange="selectOpt(this);">
                                    <option selected="selected"></option>
                                </select>
                            </div>
                                <label for="price"> Select price each:</label>
                                <input type="number" name="price" min="0" required /></br>
                                <label for="amount"> Select amount: </label>
                                <input type="number" name="amount" min="0" /></br>
                                <button type="button" onclick="newOffer();"> Confirm offer </button>
                        </div>
                    </div>
                </form>
            </div>
            <div id="my_offers">
                <table>
                    <caption> My offers </caption>
                    <thead>
                        <tr>
                            <td> Offer type </td>
                            <td> Item </td>
                            <td> Amount each </td>
                            <td> Progress </td>
                            <td </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php get_template('myOffers', $this->data['my_offers'], true);?>
                </table>
            </div>
            <div id="offers">
                <table>
                    <caption> Browse offers </caption>
                    <thead>
                        <tr>
                            <td colspan="5"><button id="sch_button"> Back to offers </button>
                                <input id="s_item" type="text" name="item" placeholder="Search for item" /></td>
                        </tr>
                        <tr>
                            <td> Item </td>
                            <td> Quantity </td>
                            <td> Price each </td>
                            <td> User </td>
                            <td> </td>
                        </tr>
                    </thead>
                    <?php get_template('offers', $this->data['offers'], true);?>
                    <tfoot>
                        <tr>
                            <td colspan="5"><button class="previous"> < Prev </button><button class="next"> Next > </button></td>
                        </tr> 
                    </tfoot>
                </table>
            </div>
            <div id="history">
                <table>
                    <thead>
                        <tr>
                            <td> Type: </td>
                            <td> Item: </td>
                            <td> Amount: </td>
                            <td> Price each: </td>
                        </tr>
                    </thead>
                <?php get_template('history', $this->data['history'], true);?>
                </table>
            </div>