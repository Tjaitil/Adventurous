            mine.css|mine.js|
            <h3 class="page_title"> Mine </h3>
            <div id="action_div" class="div_content">
                <div id="actions">
                    <p id="mining"></p>
                    <p id="time"></p>
                    <button id="cancel"> Cancel Mining </button>
                </div>
                <div id="select">
                    <?php
                    foreach($this->data['mineral_types'] as $key): ?>
                    <img class="mineral" src="<?php echo constant('ROUTE_IMG') . $key['mineral_type'] . ' ore.png';?>"
                    alt="<?php echo $key['mineral_type'];?>"/>
                    <?php endforeach;?>
                </div>
                <div id="data_container">
                    <p> Your total permits: <?php echo $this->data['minerData']['permits'];?></p>
                    <div id="data">
                        <form id="data_form">
                            <figure></figure>
                            <div class="row">
                                <label for="mineral"> Mineral: </label>
                                <input type="text" name="mineral" readonly /></br>
                            </div>
                            <label for="time"> Time: </label>
                            <input type="text" name="time" readonly /></br>
                            <label for="location"> Location: </label>
                            <input type="text" name="location" readonly /></br>
                            <label for="level"> Level: </label>
                            <input type="text" name="level" readonly /></br>                                
                            <label for="experience"> Experience: </label>
                            <input type="text" name="experience" readonly /></br>
                            <label for="permits"> Permit: </label>
                            <input type="text" name="permits" readonly /></br>
                            <label for="workforce"> Select number of workers:</label>
                            <input name="workforce" type="number" min="0" required />
                            <span>(<?php echo $this->data['workforceData']['avail_workforce']?>)</span></br>
                            <button type="button"> Mine </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>