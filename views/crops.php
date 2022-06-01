            crops.css|crops.js|
            <h1 class="page_title">Crops</h1>
            <div id="grow_crops">
                <div id="action_div" class="div_content">
                    <div id="actions" class>
                        <p class="help">
                            Select a crop type from the list to grow. </br>
                            Number of workers selected will increase the crops you get when harvesting. </br>
                            Efficiency level reduces grow time
                        </p>
                        <p id="growing"> </p>
                        <p id="time"></p>
                        <button id="harvest_action">Harvest</button>
                        <button id="cancel_action">Destroy crops</button>
                    </div>
                    <div id="action_body">
                        <div id="select">
                            <?php
                            foreach($this->data['crop_types'] as $key): ?>
                            <img class="crop_type" src="<?php echo constant('ROUTE_IMG') . $key['crop_type'] . '.png';?>"
                            alt="<?php echo $key['crop_type'];?>"/>
                            <?php endforeach;?>
                        </div>
                        <div id="data_container" class="px-1">
                            <div id="data">
                                <figure id="selected_item"></figure>
                                <form id="data_form">
                                    <label for="crop"> Crop </label>
                                    <input type="text" id="selected_crop_type" name="crop" readonly />
                                    <label for="time"> Time </label>
                                    <input type="text" name="time" readonly />
                                    <span>Efficiency reduction</span><span id="reduction_time"></span>
                                    <label for="location"> Location </label>
                                    <input type="text" name="location" readonly />
                                    <label for="level"> Level </label>
                                    <input type="text" name="level" readonly />                             
                                    <label for="experience"> Experience </label>
                                    <input type="text" name="experience" readonly />                               
                                    <label for="seeds"> Seeds </label>
                                    <input type="text" name="seeds" readonly />
                                    <label for="workforce"> Select workers (max)</label>
                                    <div class="me-auto">
                                        <input name="workforce_amount" id="workforce_amount" type="number" min="0" required />
                                        <span id="data_container_avail_workforce">
                                            (<?php echo $this->data['workforce_data']['avail_workforce']?>)
                                        </span>
                                    </div>
                                    </br>
                                </form>
                                <button type="button" class="mb-2"> Grow </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="seed_generator">
                <p>Select a item to get seeds from, NOTE that the amount can be 0</p>
                <div id="selected">
                </div>
                <input type="number" id="selected_amount" min="0" />
                <button> Generate </button>
            </div>
        