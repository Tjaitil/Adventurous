            <div id="conversation_container">
                <img class="cont_exit" src="#" />
                <h3></h3>
                <div id="conversation_flex_container">
                    <img src="#" id="conversation_a" />
                    <div id="conversation">
                      <ul>
                  
                      </ul>
                    </div>
                    <img src="#" id="conversation_b" />
                </div>
            </div>
            <canvas id="game_canvas" width="700" height="400"></canvas>
            <canvas id="game_canvas2" width="700" height="400"></canvas>
            <canvas id="game_canvas3" width="700" height="400"></canvas>
            <canvas id="text_canvas" width="700" height="400"></canvas>
            <!--<canvas id="test_canvas" width="700" height="400"></canvas>-->
            <!--<p id="demo2"></p>
            <p id="demo3"></p>-->
            <div id="inv_toggle_button_container">
               <button id="inv_toggle_button"> INV </button>
            </div>
            <div id="game_text">

            </div>
            <div id="control_text">
                <p>E</p>
                <p>W</p>
            </div>
            <div id="map_button">
                
            </div>
            <div id="inventory">
                <?php require(constant('ROUTE_VIEW') . '/inventory.php'); url();?>    
            </div>
            <div id="control">
                <button id="control_button"></button>
            </div>
            <!--<div id="demo">
                
            </div>
            <div id="demo2" style="width:50px;height:50px;border:1px solid black;">
                
            </div>-->
            <!--<div id="map">
                <img src="<?php /*echo constant('ROUTE_IMG') . '3.5.png';*/?>" />
            </div>-->
            <script src="<?php echo constant('ROUTE_JS') . 'advclient.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'canvasText.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'artefact.js';?>"></script>
            <script src="<?php echo constant("ROUTE_JS") . 'selectitem.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'select.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'warriorSelect.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'conversation.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'travel.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'progressbar.js';?>"></script>
            <script src="<?php echo constant('ROUTE_JS') . 'tutorial.js';?>"></script>
