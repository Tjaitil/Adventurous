            stockpile.css|stockpile.js|
            <h1 class="page_title">Stockpile</h1>
                <p class="help">
                    Click on items to withdraw or insert items into the stockpile. <br>
                    When selecting custom amount press enter to submit.
                </p>
            <div id="stockpile" class="div_content mb-1">
                <?php get_template('stockpile', $this->data, true);?>
            </div>
            <div id="stck_menu">
                <ul>
                    <li></li>
                    <li ontouchstart="touchMove(this);">Insert 1</li>
                    <li ontouchstart="touchMove(this);">Insert 5</li>
                    <li>
                        <input type="number" id="stck_menu_custom_amount" placeholder="">
                    </li>
                    <li ontouchstart="touchMove(this);" id="stck_menu_all">Insert all</li>
                </ul>
            </div>
