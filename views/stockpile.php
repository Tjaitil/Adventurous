            stockpile.css|stockpile.js|
            <h3 class="page_title"> Stockpile </h3>
                <p class="help">
                    Click on items to withdraw or insert items into the stockpile.
                </p>
            <div id="stockpile" class="div_content mb-1">
                <?php get_template('stockpile', $this->data, true);?>
            </div>
            <!--<div id="inventory">
                <div id="hidden">
                        <div class="inventory_buttons">
                            <button onclick="insert(this, 1);"> 1 </button>
                            <button onclick="insert(this, 3);"> 3 </button>
                            <button onclick="insert(this, 5);"> 5 </button>
                            <button onclick="withdraw(this, 'all');" id="all"> All </button>
                        </div>
                        <figure><img src="#" height="50px" witdh="50px" />
                            <figcaption></figcaption>
                        </figure>
                                    
                    </div>
            </div>-->
            <div id="stck_menu">
                <ul>
                    <li></li>
                    <li ontouchstart="touchMove(this);">Insert 1</li>
                    <li ontouchstart="touchMove(this);">Insert 5</li>
                    <li ontouchstart="touchMove(this);">Insert x</li>
                    <li ontouchstart="touchMove(this);">Insert all</li>
                </ul>
            </div>
