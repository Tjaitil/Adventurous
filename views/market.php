<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" type="text/css" href="<?php echo constant('ROUTE_CSS') . $name ?>.css" />
        <?php include(constant('ROUTE_VIEW') . 'head.php');?>
    </head>
    <body>
        <header>
            <?php require(constant('ROUTE_VIEW') . 'header.php'); ?>
        </header>
        <section>
            <?php require(constant('ROUTE_VIEW') . 'layout.php'); ?>
            <h3 class="page_title"> Market </h3>
            <button onclick="show('offers');"> Offers </button>
            <button onclick="show('my_offers');"> My Offers </button>
            <button onclick="show('history');"> History </button>
            <div id="new_off">
                <form id="offer_form">
                    <label for="type"> Buy/Sell: </label>
                    <select id="form_select" name="type" onchange="toggleType();">
                        <option selected="selected">  </option>
                        <option value="Buy"> Buy </option>
                        <option value="Sell"> Sell </option></br>
                    </select>
                    <div id="form_cont">
                        <div id="item">
                            <div id="selected">
                                <div></div>
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
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . 'inventory.php'); url();?>
            </div>
            <div id="my_offers">
                <table>
                    <thead>
                        <tr>
                            <td> Offer type: </td>
                            <td> Item: </td>
                            <td> Amount: </td>
                            <td> Progress </td>
                        </tr>
                    </thead>
                    <?php get_template('myOffers', $this->data['my_offers']);?>
                </table>
            </div>
            <div id="offers">
                <table>
                    <thead>
                        <tr>
                            <td colspan="4"><button id="sch_button"> Back to offers </button>
                                <input id="s_item" type="text" name="item" placeholder="Search for item" /></td>
                        </tr>
                        <tr>
                            <td> Item: </td>
                            <td> Quantity: </td>
                            <td> Price each: </td>
                            <td> User: </td>
                        </tr>
                    </thead>
                    <?php get_template('offers', $this->data['offers']);?>
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
                <?php get_template('history', $this->data['history']);?>
                </table>
            </div>
            <script src="<?php echo constant('ROUTE_JS') . $name . '.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'selectitem.js';?> "></script>
        </section>
        <aside>
            <?php require(constant('ROUTE_VIEW') . '/aside.php'); ?>
        </aside>
    </body>
</html>
