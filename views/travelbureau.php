            travelbureau.css|travelbureau.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <!--<table id="horse_shop">
                <caption> Horse Shop </caption>
                <thead><tr>
                    <td>Horse type</td>
                    <td>Cost</td>
                    <td>Stock</td>
                    <td></td>
                </tr></thead>
                <?php /*get_template('horseShop', $this->data);*/ ?>
            </table>-->
            <div id="cart_shop">
                <p> Your current cart: <?php echo $this->data['cart'] . ' cart';?></p>
                <table>
                    <caption> Cart Shop </caption>
                    <thead>
                        <tr>
                            <td> Cart wheel </td>
                            <td> Cart wood </td>
                            <td> Gold </td>
                            <td> Capasity </td>
                            <td> Mineral required </td>
                            <td> Wood required </td>
                            <td></td>
                        </tr>
                    </thead>
                    <?php get_template('cartShop', $this->data, true); ?>
                </table>
            </div>