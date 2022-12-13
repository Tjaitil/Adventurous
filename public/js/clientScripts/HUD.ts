import { GamePieces } from "./gamePieces.js";
import { tutorial } from "./tutorial.js";
import { ProgressBar } from "../progressBar.js";
import viewport from "./viewport.js";

export const HUD = {
    container: null,
    elements: {
        // controlText: new HTMLElement(),
        controlText: null,
        // hungerProgressBar: new HTMLElement(),
        hungerProgressBar: null as ProgressBar,
        // healthProgressBar: new HTMLElement(),
        healthProgressBar: null as ProgressBar,
        // helpContainer: new HTMLElement(),
        helpContainer: null,
        // huntedIcon: new HTMLElement(),
        huntedIcon: null,
        // huntedLocator: new HTMLElement(),
        huntedLocator: null,
        // iconContainer: new HTMLElement(),
        iconContainer: null,
        tutorialProgressBar: null as ProgressBar,
    },
    setup(width: number, height: number, top: number, left: number) {
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
            document.getElementById("game_canvas").offsetTop +
            document.getElementById("game_canvas").offsetHeight -
            conversation_container.offsetHeight -
            8 +
            "px";
        document.getElementById("news_content").style.width = width + "px";

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
        let hungerProgressBar = document.getElementById("hunger_progressBar");
        hungerProgressBar.style.top = HUDTopPosition + HUDrowHeight * 0 + "px";
        hungerProgressBar.style.left = HUDLeftPosition + "px";
        hungerProgressBar.style.width = "250px";

        let currentHunger = parseInt(hungerProgressBar.querySelectorAll(".progressBar_currentValue")[0].innerHTML);
        this.elements.hungerProgressBar = new ProgressBar(document.getElementById("hunger_progressBar"), {
            currentValue: currentHunger,
            maxValue: 100,
        });

        let healthProgressBar = document.getElementById("health_progressBar");
        healthProgressBar.style.top = HUDTopPosition + HUDrowHeight * 0 + "px";
        healthProgressBar.style.left = parseInt(hungerProgressBar.style.width) + 30 + "px";
        healthProgressBar.style.width = "100px";
        healthProgressBar.querySelectorAll(".progressBar_currentValue")[0].innerHTML = "" + GamePieces.player.health;
        this.elements.healthProgressBar = new ProgressBar(document.getElementById("health_progressBar"), {
            currentValue: 100,
            maxValue: 100,
        });

        // Position help button and help container
        let help_button = document.getElementById("HUD_help_button");
        help_button.style.top = HUDTopPosition + HUDrowHeight * 0 + "px";
        help_button.style.left = width - 10 - help_button.offsetWidth + "px";

        // Assign hunted icon and hunted locator
        this.elements.huntedIcon = document.getElementById("HUD_hunted_icon");
        this.elements.huntedIcon.style.top = 70 + "px";
        this.elements.huntedIcon.style.left = HUDLeftPosition + "px";

        this.elements.huntedLocator = document.getElementById("HUD_hunted_locater");
        this.elements.huntedLocator.style.top =
            this.elements.huntedIcon.offsetHeight + this.elements.huntedIcon.offsetTop + 10 + "px";
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

        toggle_map_icon.style.top = HUDTopPosition + HUDrowHeight * 0 + "px";
        toggle_map_icon.style.left = width - toggle_map_icon.offsetWidth - 20 - toggle_map_icon.offsetWidth - 25 + "px";

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
    },
    makeTutorialHUD() {
        let tutorial_progressContainer = document.createElement("div");
        tutorial_progressContainer.setAttribute("id", "tutorial_progressContainer");
        tutorial_progressContainer.style.top = 60 + "px";
        tutorial_progressContainer.style.width = viewport.width * 0.75 + "px";
        let title = document.createElement("p");
        title.innerText = "Tutorial progress";
        let under_title = document.createElement("p");
        under_title.innerText = "0. Introduction";

        // Create new progressBar and append in to progressContainer
        this.elements.tutorialProgressBar = new ProgressBar(document.getElementById("tutorial_progressBar"), {
            currentValue: tutorial.step,
            maxValue: tutorial.lastStep,
        });
        tutorial_progressContainer.appendChild(title);
        tutorial_progressContainer.appendChild(this.elements.tutorialProgressBar.progressBarElement);
        tutorial_progressContainer.appendChild(under_title);
        document.getElementById("game_hud").appendChild(tutorial_progressContainer);

        // Make exit button
        let button = document.createElement("button");
        button.appendChild(document.createTextNode("Skip tutorial"));
        button.addEventListener("click", tutorial.skipTutorial);
        button.style.position = "absolute";
        button.style.top = 130 + "px";
        button.style.left = document.getElementById("game_canvas").offsetLeft + "px";
        button.style.zIndex = "" + 4;
        document.getElementById("game_hud").appendChild(button);
    },
    hideTutorialHUD() {
        document.getElementById("game_hud").removeChild(document.getElementById("tutorial_progressContainer"));
    },
};
