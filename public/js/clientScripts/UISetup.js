const UISetup = function () {
    let newWidth;
    if (screen.width < 800) {
        newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.97;
    }
    else {
        newWidth = document.getElementsByTagName("section")[0].offsetWidth * 0.68;
    }
    let newHeight;
    // If the device is mobile check for the shortest dimension of height and width to compensate for already rotated devices
    if (game.properties.device == "mobile") {
        newHeight = (screen.width < screen.height) ? screen.width - 20 : screen.height - 20;
        console.log(newHeight);
    }
    else {
        newHeight = screen.height - 20;
    }
    if (newHeight > 600) {
        newHeight = 550;
    }
    game.properties.canvasWidth = newWidth;
    game.properties.canvasHeight = newHeight;
    // Set width on elements that should be same with as canvas
    let conversation_container = document.getElementById("conversation_container");
    conversation_container.style.width = newWidth + "px";
    conversation_container.style.top =
    (document.getElementById("game_canvas").offsetTop + document.getElementById("game_canvas").height) + "px";
    document.getElementById("news_content").width = newWidth + "px";
    // Align all canvases
    let gameCanvas = document.querySelectorAll("canvas");
    for (var i = 0; i < gameCanvas.length; i++) {
        gameCanvas[i].width = newWidth;
        gameCanvas[i].height = newHeight;
        if (i > 0) {
            gameCanvas[i].style.top = gameCanvas[0].offsetTop + "px";
            gameCanvas[i].style.left = gameCanvas[0].offsetLeft + "px";
        }
    }
    let canvas_border = document.getElementById("canvas_border");
    canvas_border.style.top = gameCanvas[0].offsetTop + "px";
    canvas_border.style.width = newWidth + "px";
    canvas_border.style.left = gameCanvas[0].offsetLeft + "px";
    game.properties.context.scale(viewport.zoom, viewport.zoom);
    game.properties.context2.scale(viewport.zoom, viewport.zoom);
    game.properties.context3.scale(viewport.zoom, viewport.zoom);
    game.properties.context4.scale(viewport.zoom, viewport.zoom);

    game.properties.charX = (Math.floor((newWidth / 2) - gamePieces.player.playerSize)) / viewport.zoom;
    game.properties.charY = Math.floor((newHeight / 2) - gamePieces.player.playerSize) / viewport.zoom;
    console.log(document.getElementById("control").style.display);
    if (document.getElementById("control").style.display === "blockc") {
        let control = document.getElementById("control");
        control.style.top = gameCanvas[0].offsetTop + game.properties.canvasHeight - 125 + "px";
        control.style.left = "10px";
        document.getElementById("inventory").style.top = gameCanvas[0].offsetTop + "px";
    }
    let control_text = document.getElementById("control_text");
    control_text.style.top = gameCanvas[0].offsetTop + game.properties.canvasHeight - control_text.offsetHeight + "px";
    console.log(control_text.offsetHeight);
    console.log(control_text.getBoundingClientRect());
    console.log(control_text.clientHeight);
    control_text.style.left = gameCanvas[0].offsetLeft + 20 + "px";
    document.getElementById("game_text").style.maxWidth = game.properties.canvasWidth + "px";
    let HUDTopPosition = 15;
    let HUDLeftPosition = 10;
    let HUDrowHeight = 46;
    // Position canvas HUD
    document.getElementById("game_hud").style.width = newWidth + "px";

    // Position the hunger bar
    let hunger_progressBar = document.getElementById("hunger_progressBar");
    hunger_progressBar.style.top = HUDTopPosition + (HUDrowHeight * 0) +  "px";
    hunger_progressBar.style.left = HUDLeftPosition + "px";
    hunger_progressBar.style.width = "250px";
    progressBar.calculateProgress(document.getElementById("hunger_progressBar"), false, 100, true);

    let health_progressBar = document.getElementById("health_progressBar");
    health_progressBar.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    health_progressBar.style.left = parseInt(hunger_progressBar.style.width) + 30 + "px";
    health_progressBar.style.width = "100px";
    health_progressBar.querySelectorAll(".progressBar_currentValue")[0].innerHTML = gamePieces.player.health;
    progressBar.calculateProgress(health_progressBar, false, false, true);

    // Position help button and help container
    let help_button = document.getElementById("HUD_help_button");
    help_button.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    help_button.style.left = newWidth - 10 - help_button.offsetWidth + "px";

    // Assign hunted icon and hunted locator
    let hunted_icon = document.getElementById("HUD_hunted_icon");
    hunted_icon.style.top = 70 + "px";
    hunted_icon.style.left = HUDLeftPosition + "px";
    let hunted_locater = document.getElementById("HUD_hunted_locater");
    hunted_locater.style.top = hunted_icon.offsetHeight + hunted_icon.offsetTop + 10 + "px";
    hunted_locater.style.left = HUDLeftPosition + "px";

    // Assign help container width as canvas
    let help_container = document.getElementById("client_help_container");
    let settings_container = document.getElementById("client_settings_container");
    help_container.style.width = settings_container.style.width = newWidth + "px";
    help_container.style.top = settings_container.style.top = gameCanvas[0].offsetTop + 40 + "px";

    // Position map related elements
    // document.getElementById("toggle_map_icon").style.top = "10px";
    let HUD_left_icons_container = document.getElementById("HUD-left-icons-container");
    HUD_left_icons_container.style.left = newWidth - HUD_left_icons_container.offsetWidth - 10 + "px";
    let toggle_map_icon = document.getElementById("toggle_map_icon");
    toggle_map_icon.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    toggle_map_icon.style.left = newWidth - toggle_map_icon.style.width -
        20 - toggle_map_icon.offsetWidth - 25 + "px";
    let mapContainer = document.getElementById("map_container");
    mapContainer.style.top = gameCanvas[0].offsetTop + "px";
    mapContainer.style.left = "98%";

    // Assign width to log_2 equal to canvas width
    document.getElementById("log_2").style.width = newWidth + "px";
    /* If screen is less than 830 set sidebar to be the same top as inventory so that the two are aligned
     * Also align cont_exit button in news content to middle instead of right */
    if (window.screen.width < 830) {
        document.getElementById("sidebar").style.top = gameCanvas[0].offsetTop + "px";
        document.getElementById("inv_toggle_button_container").style.top = gameCanvas[0].offsetTop + "px";
        let cont_exit_button = document.getElementById("cont_exit");
        cont_exit_button.style.zIndex = "1";
        cont_exit_button.style.cssFloat = "";
        cont_exit_button.style.margin = "0 auto";
        cont_exit_button.style.marginBottom = "20px";
    }

    setTimeout(() => {
        document.getElementById("client-container").style.opacity = 1;
        document.getElementById("client-loading-container").style.display = "none";
        game.loadGame();
    }, 5000);
};
// function loadingPage() {
//     let text = document.getElementById("client-loading-container").getElementsByTagName("h1")[1].innerText;
//     if(text === "...") {
//         text = "";
//     } else {
//         text += ".";
//     }
//     document.getElementById("client-loading-container").getElementsByTagName("h1")[1].innerText = text;
// }