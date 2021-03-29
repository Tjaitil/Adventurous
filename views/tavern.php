            tavern.css|tavern.js|
            <h3 class="page_title"> Tavern </h3>
            <div id="persons">
                <?php /*if(empty($this->data['user_tavern_data']['persons'])) */ ?>
                <!--<div> No persons of relevance to talk to in tavern </div>-->
                <?php /*endif;*/ ?>
                <?php /*foreach($this->data['user_tavern_data']['persons'] as $key): ?>
                    <div><img src="#" width="50px" height="50px" /><?php echo ucfirst($key); ?>
                    <button onclick="talk('<?php echo ucfirst($key); ?>', '0');">Talk</button></div>
                <?php endforeach;*/?>
            </div>
            </br>
            <?php get_template('tavern', $this->data['tavern']['workers'], true); ?>
            <div id="eat">
                <div id="selected">
                    
                </div>
                <input type="number" min="0" />
                <button> Eat </button>
                <p id="item_healing_amount"></p>
            </div>