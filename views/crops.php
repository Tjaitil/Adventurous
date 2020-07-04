            crops.css|crops.js%select.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="action_div">
                <div id="actions">
                    <p id="growing"> </p></br>
                    <p id="time"></p>
                    <button onmousedown="destroyCrops();"> Destroy crops </button>
                </div>
                <div id="select">
                    <?php
                    foreach($this->data['crop_types'] as $key): ?>
                    <img class="crop" src="<?php echo constant('ROUTE_IMG') . $key['crop_type'] . '.png';?>"
                    alt="<?php echo $key['crop_type'];?>"/>
                    <?php endforeach;?>
                </div>
                <div id="data_container">
                    <div id="data">
                        <form id="data_form">
                            <figure></figure>
                            <div class="row">
                                <label for="crop"> Crop: </label>
                                <input type="text" name="crop" readonly /></br>
                            </div>
                            <label for="time"> Time: </label>
                            <input type="text" name="time" readonly /></br>
                            <label for="seeds"> Seeds: </label>
                            <input type="text" name="seeds" readonly /></br>
                            <label for="workforce"> Workforce:</label>
                            <input name="workforce" id="" type="number" min="0" required />
                            <span>(<?php echo $this->data['workforce_data']['avail_workforce']?>)</span>
                            <button type="button"> Grow </button>
                        </form>
                    </div>
                </div>
            </div>
            <div id="crops_action">
                <div id="growing">
                    <p id="time"></p></br>
                </div>
                Actions: 
                <button onmousedown="destroyCrops();"> Destroy crops </button>
                Plant:
                <form id="plant">
                    <label for="type"> Select crop type:</label>
                    <select name="type" id="form_select" onchange="img();" required>
                        <option></option>
                        <?php
                            $crop_types = array_column($this->data['crop_types'], 'crop_type');
                            foreach($crop_types as $key): ?>
                                <option value="<?php echo $key;?>"><?php echo ucfirst($key);?></option>
                            <?php endforeach;?>
                    </select><img src="#" id="type_img" height="50px" width="50px"/></br>
                    <label for="quantitiy"> Select amount of fields: </label>
                    <input name="quantity" id="crop_quantity" type="number" min="0" required />
                    <span>(<?php echo $data['fields']['fields_avail']; ?>)</span></br>
                    <label for="workfore"> Select amount of workers:</label>
                    <input name="workforce" id="crop_workforce" type="number" min="0" required />
                    (<?php echo $this->data['workforce_data']['avail_workforce'];?>)</br>
                    <!---<label for="estimated"> Estimated time:</label>
                    <input name="estimated" id="plant_estimated" type="text" min="0" />-->
                    <button type="button" id="plant_button"> Grow </button>
                </form>
            </div>
            <div id="seed_g">
                <p>Select a item to get seeds from:</p>
                <div id="selected">
                    <div id="selected_t"></div>
                </div>
                <input type="number" id="amount" min="0" />
                <button> Generate </button>
            </div>
        