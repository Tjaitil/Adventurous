            crops.css|crops.js%select.js|
            <h3 class="page_title"><?php echo $title;?></h3>
            <div id="grow_crops">
                <div id="action_div" class="div_content">
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
                                <label for="location"> Location: </label>
                                <input type="text" name="location" readonly /></br>
                                <label for="level"> Level: </label>
                                <input type="text" name="level" readonly /></br>                                
                                <label for="experience"> Experience: </label>
                                <input type="text" name="experience" readonly /></br>                                
                                <label for="seeds"> Seeds: </label>
                                <input type="text" name="seeds" readonly /></br>
                                <label for="workforce"> Workforce:</label>
                                <input name="workforce" id="" type="number" min="0" required />
                                <span>(<?php echo $this->data['workforce_data']['avail_workforce']?>)</span>
                                </br>
                                <button type="button"> Grow </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="seed_generator">
                <p>Select a item to get seeds from, NOTE that the amount can be 0</p>
                <div id="selected">
                    <div id="selected_t"></div>
                </div>
                <input type="number" id="selected_amount" min="0" />
                <button> Generate </button>
            </div>
        