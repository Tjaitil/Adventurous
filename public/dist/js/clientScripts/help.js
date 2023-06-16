import { Game } from '../advclient.js';
export const clientHelpContainer = {
    init() {
        clientHelpContainer.element = document.getElementById("client_help_container");
        clientHelpContainer.element.querySelectorAll(".cont_exit")[0].addEventListener("click", () => clientHelpContainer.toggle());
        document.getElementById("HUD_help_button").addEventListener("click", () => clientHelpContainer.toggle());
    },
    toggled: false,
    element: null,
    toggle() {
        if (this.toggled === false) {
            this.element.style.visibility = "visible";
            Game.setGameState('help');
        }
        else {
            this.element.style.visibility = "hidden";
            Game.setGameState('playing');
        }
        this.toggled = !this.toggled;
    }
};
