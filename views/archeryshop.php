            #|archeryshop.js|
            <h1 class="page_title">Archery Shop</h1>
            <div id="fletch">
                <p class="help">
                    Craft bows, unfinished arrows or arrow shafts from logs. Some bows will require a certain total
                    level of warriors. check <a href="gameguide/warrior">gameguide</a>
                </p>
                <?php 
                    get_template("storeContainer", false); 
                    createStoreContainer(array("container_items" => $this->data), 
                                        ["item_requirements" => false]);
                ?>
            </div>