            market.css|market.js|
            <h3 class="page_title"> Market </h3>
            <div id="new_off">
                <form id="offer_form">
                    <div id="offer_types">
                        <p> Select type of offer:</p>
                        <button>
                            Buy
                        </button>
                        <button>
                            Sell
                        </button>
                    </div>
                    <div id="form_cont">
                        <div id="item">
                            <div id="selected">
                            
                            </div>
                            <input type="text" id="item_name" name="item" placeholder="Item Name" readonly />
                        </div>
                        <div id="form_inputs">
                            <div id="item_b">
                                <label for="item_srch"> Search name: </label>
                                <input id="item_srch" type="text" /></br>
                                <label for="item_name">
                                    Select item:   
                                </label></br>
                                <select name="item_name" id="select_item">
                                    <option selected="selected"></option>
                                </select>
                            </div>
                                <label for="price"> Select price each:</label></br>
                                <input type="number" name="price" min="0" required /></br>
                                <label for="amount"> Select amount: </label></br>
                                <input type="number" name="amount" min="0" /></br>
                                <p></p>
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
                            <td> Collect Item(s) </td>
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
                            <td colspan="5">
                                <button class="previous button_disabled"> < Prev </button>
                                <button class="next"> Next > </button>
                            </td>
                        </tr> 
                    </tfoot>
                </table>
            </div>
            <div id="history">
                <table>
                    <thead>
                        <tr>
                            <td> Type </td>
                            <td> Item </td>
                            <td> Amount </td>
                            <td> Price each </td>
                        </tr>
                    </thead>
                <?php get_template('history', $this->data['history'], true);?>
                </table>
            </div>