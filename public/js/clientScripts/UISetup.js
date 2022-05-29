const HUD = {
    container: null,
    elements: {
        // controlText: new HTMLElement(),
        controlText: null,
        // hungerProgressBar: new HTMLElement(),
        hungerProgressBar: null,
        // healthProgressBar: new HTMLElement(),
        healthProgressBar: null,
        // helpContainer: new HTMLElement(),
        helpContainer: null,
        // huntedIcon: new HTMLElement(),
        huntedIcon: null,
        // huntedLocator: new HTMLElement(),
        huntedLocator: null,
        // iconContainer: new HTMLElement(),
        iconContainer: null,
    },
    setup(width, height, top, left) {
    
    // Setup game_hud container;
    let container = document.getElementById("game_hud");
    container.style.top = top + "px";
    container.style.left = left + "px";
    container.style.width = width + "px";
    container.style.height = height + "px";

    let HUDTopPosition = 15;
    let HUDLeftPosition = 10;
    let HUDrowHeight = 46;
    
    // Set width on elements that should be same with as canvas
    let conversation_container = document.getElementById("conversation_container");
    conversation_container.style.width = width + "px";
    conversation_container.style.top =
    (document.getElementById("game_canvas").offsetTop + document.getElementById("game_canvas").offsetHeight
        - conversation_container.offsetHeight - 8) + "px";
    document.getElementById("news_content").width = width + "px";

    if (document.getElementById("control").style.display === "block") {
        let control = document.getElementById("control");
        control.style.top = top + left - 125 + "px";
        control.style.left = "10px";
        document.getElementById("inventory").style.top = top + "px";
    }
    let control_text = document.getElementById("control_text");
    control_text.style.top = height - control_text.offsetHeight - 20 + "px";
    control_text.style.left = left + 20 + "px";
    
    // Position the hunger bar
    this.elements.hungerProgressBar = document.getElementById("hunger_progressBar");
    this.elements.hungerProgressBar.style.top = HUDTopPosition + (HUDrowHeight * 0) +  "px";
    this.elements.hungerProgressBar.style.left = HUDLeftPosition + "px";
    this.elements.hungerProgressBar.style.width = "250px";
    progressBar.calculateProgress(this.elements.hungerProgressBar, false, 100, true);

    this.elements.healthProgressBar = document.getElementById("health_progressBar");
    this.elements.healthProgressBar.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    this.elements.healthProgressBar.style.left = parseInt(hunger_progressBar.style.width) + 30 + "px";
    this.elements.healthProgressBar.style.width = "100px";
    this.elements.healthProgressBar.querySelectorAll(".progressBar_currentValue")[0].innerHTML = gamePieces.player.health;
    progressBar.calculateProgress(this.elements.healthProgressBar, false, false, true);

    // Position help button and help container
    let help_button = document.getElementById("HUD_help_button");
    help_button.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    help_button.style.left = width - 10 - help_button.offsetWidth + "px";

    // Assign hunted icon and hunted locator
    this.elements.huntedIcon = document.getElementById("HUD_hunted_icon");
    this.elements.huntedIcon.style.top = 70 + "px";
    this.elements.huntedIcon.style.left = HUDLeftPosition + "px";

    this.elements.huntedLocator = document.getElementById("HUD_hunted_locater");
    this.elements.huntedLocator.style.top = this.elements.huntedIcon.offsetHeight + 
                                            this.elements.huntedIcon.offsetTop + 10 + "px";
    this.elements.huntedLocator.style.left = HUDLeftPosition + "px";

    // Assign help container width as canvas
    let help_container = document.getElementById("client_help_container");
    let settings_container = document.getElementById("client_settings_container");
    help_container.style.width = settings_container.style.width = width + "px";
    help_container.style.top = settings_container.style.top = top + 40 + "px";

    // Position map related elements
    // document.getElementById("toggle_map_icon").style.top = "10px";
    this.elements.iconContainer = document.getElementById("HUD-left-icons-container");
    this.elements.iconContainer.style.left = width - this.elements.iconContainer.offsetWidth - 10 + "px";
    let toggle_map_icon = document.getElementById("toggle_map_icon");
    toggle_map_icon.style.top = HUDTopPosition + (HUDrowHeight * 0) + "px";
    toggle_map_icon.style.left = width - toggle_map_icon.style.width -
        20 - toggle_map_icon.offsetWidth - 25 + "px";
    let mapContainer = document.getElementById("map_container");
    mapContainer.style.top = top + "px";
    mapContainer.style.left = "98%";

    // Assign width to log_2 equal to canvas width
    document.getElementById("log_2").style.width = width + "px";
    /* If screen is less than 830 set sidebar to be the same top as inventory so that the two are aligned
        * Also align cont_exit button in news content to middle instead of right */
    if (window.screen.width < 830) {
        document.getElementById("sidebar").style.top = top + "px";
        document.getElementById("inv_toggle_button_container").style.top = top + "px";
        let cont_exit_button = document.getElementById("cont_exit");
        cont_exit_button.style.zIndex = "1";
        cont_exit_button.style.cssFloat = "";
        cont_exit_button.style.margin = "0 auto";
        cont_exit_button.style.marginBottom = "20px";
    }
    }
};