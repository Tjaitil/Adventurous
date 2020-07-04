            travelbureau.css|travelbureau.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <table id="horse_shop">
                <thead><tr>
                    <td>Horse type</td>
                    <td>Cost</td>
                    <td>Stock</td>
                    <td></td>
                </tr></thead>
                <?php get_template('horseShop', $data); ?>
            </table>
            <table id="cart_shop">
                <thead>
                    <tr>
                        <td> Cart wheel </td>
                        <td> Cart wood </td>
                        <td> Gold </td>
                        <td> Capasity </td>
                        <td> Stock </td>
                        <td> Mineral Amount </td>
                        <td> Wood Amount </td>
                        <td></td>
                    </tr>
                </thead>
                <?php get_template('cartShop', $data); ?>