            <?php

            use App\libs\TemplateFetcher;

            ?>
            <div class="log_3"></div>
            <script>
                window.addEventListener("load", () => {
                    var x = setInterval(function() {
                        if (document.getElementById("client-loading-container")) {
                            let text = document.getElementById("client-loading-container").getElementsByTagName("h1")[1].innerText;
                            if (text === "...") {
                                text = "";
                            } else {
                                text += ".";
                            }
                            document.getElementById("client-loading-container").getElementsByTagName("h1")[1].innerText = text;
                        } else {
                            clearInterval(x);
                        }
                    }, 1000);
                });
            </script>
            <div id="client-loading-container">
                <h1>Loading client</h1>
                <h1>...</h1>
            </div>

            <div id="client-container">
                <?php

                require(constant('ROUTE_VIEW') . 'layout.php'); ?>
                <div id="conversation_container" class="div_content">
                    <img class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png'; ?>" />
                    <h3 id="conversation_header"></h3>
                    <div id="conversation_flex_container">
                        <img src="#" id="conversation_a" />
                        <div id="conversation">
                            <ul>
                            </ul>
                            <button id="conv_button">Click here to continue</button>
                        </div>
                        <img src="#" id="conversation_b" />
                    </div>
                </div>
                <div id="game-screen">

                    <div id="canvas_border"></div>
                    <canvas id="game_canvas" width="700" height="400"></canvas>
                    <canvas id="game_canvas2" width="700" height="400"></canvas>
                    <canvas id="game_canvas3" width="700" height="400"></canvas>
                    <canvas id="game_canvas4" width="700" height="400"></canvas>
                    <canvas id="text_canvas" width="700" height="400"></canvas>
                    <canvas id="hud_canvas" width="700" height="400"></canvas>
                </div>
                <div id="client_help_container" class="div_content div_content_dark">
                    <div id="client_help_content">
                        <img class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png'; ?>" />
                        <h1 class="page_title"> Help </h1>
                        </br>
                        <div id="help_content_introduction" class="mb-1">
                            Here is some short information to help you out. More extensive info can be found on /gameguide.
                        </div>
                        <div class="help_content_section">
                            <h4 class="help_content_section">Hunger</h4>
                            <p class="help_content_section_p">
                                Every time you grow crops, start mining, start a new assignment or a new army mission the hunger
                                will increase. To decrease hunger e.g increase hunger bar you have to eat food. Visit tavern found
                                in every city to eat.
                            </p>
                        </div>
                    </div>
                </div>
                <div id="client_settings_container" class="div_content div_content_dark">
                    <div id="client_help_content">
                        <img class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png'; ?>" />
                        <h1 class="page_title"> Settings </h1>
                        <label class="label-container">
                            Minimal Controls? <br>
                            This will remove "P" and "C" section
                            <input type="checkbox" name="client-settings-minimal-control" id="client-settings-minimal-control">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                </div>

                <div id="game_hud">
                    <div id="hunger_progressBar" class="progressBarContainer">
                        <div class="progressBarOverlayShadow">
                        </div>
                        <div class="progressBarOverlay">
                        </div>
                        <div class="progressBar">
                            <span class="progressBar_currentValue"><?php echo $current_hunger; ?></span>
                            &nbsp/&nbsp
                            <span class="progressBar_maxValue">100</span>
                        </div>
                    </div>
                    <div id="health_progressBar" class="progressBarContainer">
                        <div class="progressBarOverlayShadow">
                        </div>
                        <div class="progressBarOverlay">
                        </div>
                        <div class="progressBar">
                            <span class="progressBar_currentValue">100</span>
                            &nbsp/&nbsp
                            <span class="progressBar_maxValue">100</span>
                        </div>
                    </div>
                    <img src="<?php echo constant('ROUTE_IMG') . 'hunted icon.png'; ?>" id="HUD_hunted_icon" />
                    <p id="HUD_hunted_locater"></p>
                    <div id="HUD-left-icons-container">
                        <img class="HUD-icon" id="toggle_map_icon" src="<?php echo constant('ROUTE_IMG') . 'globe.png'; ?>" />
                        <img class="HUD-icon" id="HUD_help_button" src="<?php echo constant('ROUTE_IMG') . 'help icon.png'; ?>" />
                        <img class="HUD-icon" id="setting_button" src="<?php echo constant('ROUTE_IMG') . 'settings icon.png'; ?>" />
                    </div>
                    <div id="control_text">
                        <p class="extendedControls">C - Toggle Attack Mode</p>
                        <p class="extendedControls">P - Pause</p>
                        <p>A - Attack</p>
                        <p id="control_text_building">E -</p>
                        <p id="control_text_conversation">W -</p>
                    </div>
                </div>
                <div id="game_text">
                </div>
                <div id="map_container">
                    <div id="map_container_header">
                        <img id="toggle_icon_list_image" class="cur-pointer" src="<?php echo constant('ROUTE_IMG') . 'symbol icon.png'; ?>">
                        <div id="map_type_toggle_container">
                            <div id="map_type_toggle_overlay"></div>
                            <img id="toggle_world_image" src="<?php echo constant("ROUTE_IMG") . 'globe.png' ?>" />
                        </div>
                        <h2 id="map_header"> Local map </h2>
                        <img id="close_map_button" class="cont_exit" src="<?php echo constant("ROUTE_IMG") . 'exit.png'; ?>" width="20px" height="20px" />
                    </div>
                    <div id="map_icon_list">
                        <ul>
                            <li><img src="<?php echo constant('ROUTE_IMG') . 'travel icon.png'; ?>" alt="travel_icon">
                                <span> Travel with Pesr </span>
                            </li>
                            <li>
                                <img src="<?php echo constant('ROUTE_IMG') . 'boat travel icon.png'; ?>" alt="boat travel icon">
                                <span> Travel with boat </span>
                            </li>
                            <li>
                                <img src="<?php echo constant('ROUTE_IMG') . 'combat icon.png'; ?>" alt="combat icon">
                                <span> Daqloon obelisk </span>
                            </li>
                        </ul>
                    </div>
                    <div id="map_local_img_container">
                        <img id="local_img" src="#" />
                        <span id="map_player_marker"></span>
                    </div>
                    <div id="map_world_img_container">
                        <?php
                        $y = 1;
                        $x = 1;
                        for ($i = 0; $i < 90; $i++) : ?>
                            <img class="world_img" alt="map img" src="<?php echo constant("ROUTE_IMG") . $x . '.' . $y . 'm.png'; ?>">
                        <?php
                            $x++;
                            if ($x == 10) {
                                $x = 1;
                                $y++;
                            }
                        endfor;
                        ?>
                    </div>
                </div>
                <div id="inv_toggle_button_container">
                    <button id="inv_toggle_button"> INV </button>
                </div>
                <div id="inventory">
                    <?php fetchTemplate('inventory', $data['inventory']); ?>
                </div>
                <div id="control">
                    <button id="control_button"></button>
                </div>
            </div>
            <script defer src="<?php echo constant('ROUTE_JS') . 'advclient.js'; ?>" type="module"></script>
            <script defer src="<?php echo constant('ROUTE_JS') . 'clientScripts/getXp.js'; ?>" type="module"></script>