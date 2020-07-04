            tavern.css|tavern.js|
            <h3 class="page_title"> Tavern </h3>
            <div id="persons">
                <?php if(empty($data['persons'])):?>
                <div> No persons of relevance to talk to in tavern </div>
                <?php endif; ?>
                <?php foreach($data['persons'] as $key): ?>
                    <div><img src="#" width="50px" height="50px" /><?php echo ucfirst($key); ?>
                    <button onclick="talk('<?php echo ucfirst($key); ?>', '0');">Talk</button></div>
                <?php endforeach;?>
            </div>
            </br>
            <?php get_template('tavern', $data['workers']); ?>
            <div id="eat">
                <div id="selected">
                    
                </div>
                <input type="number" min="0" />
                <button> Eat </button>
            </div>
            <div id="curtain">
                <div id="conversation">
                <img id="" src="#"  width="20px" height="20px" onclick="close();" />
                <img id="conv_a" src="#" />
                <p id="conv"></p>
                <button id="conv_button" onclick="talk(0, '1');"> Click here to continue </button>
                <img id="conv_b" src="#" />
                </div>
            </div>