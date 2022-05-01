            mine.css|mine.js|
            <h1 class="page_title">Mine</h1>
            <div id="action_div" class="div_content">
                <div id="actions">
                    <p id="mining"></p>
                    <p id="time"></p>
                    <button id="cancel_action"> Cancel Mining </button>
                </div>
                <div id="action_body">
                    <div id="select">
                        <?php
                    foreach($this->data['mineral_types'] as $key): ?>
                    <img class="mineral" src="<?php echo constant('ROUTE_IMG') . $key['mineral_type'] . ' ore.png';?>"
                    alt="<?php echo $key['mineral_type'];?>"/>
                    <?php endforeach;?>
                    </div>
                    <div id="data_container">
                        <div id="data">
                            <p> Your total permits: <?php echo $this->data['minerData']['permits'];?></p>
                            <figure id="selected_item"></figure>
                            <form id="data_form">
                                <label for="mineral"> Mineral </label>
                                <input type="text" name="mineral" readonly />
                                <label for="time"> Time: </label>
                                <input type="text" name="time" readonly />
                                <span>Efficiency reduction</span><span id="reduction_time"></span>
                                <label for="location"> Location </label>
                                <input type="text" name="location" readonly />
                                <label for="level"> Level </label>
                                <input type="text" name="level" readonly />                                
                                <label for="experience"> Experience </label>
                                <input type="text" name="experience" readonly />
                                <label for="permits"> Permit </label>
                                <input type="text" name="permits" readonly />
                                <label for="workforce"> Select workers (max)</label>
                                <div>    
                                    <input name="workforce" type="number" min="0" required />
                                    <span id="data_container_avail_workforce">
                                        (<?php echo $this->data['workforce_data']['avail_workforce']?>)
                                    </span></br>
                                </div>
                            </form>
                            <button type="button"> Mine </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>