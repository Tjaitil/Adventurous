window.addEventListener('load', function() {  
    clientHelpContainer.element = document.getElementById("client_help_container");
    clientHelpContainer.element.querySelectorAll(".cont_exit")[0].addEventListener("click", () => clientHelpContainer.toggle());
    document.getElementById("HUD_help_button").addEventListener("click", () => clientHelpContainer.toggle());
});
const clientHelpContainer = {
    toggled: false,
    element: null,
    toggle() {
        if(this.toggled === false) {
            this.element.style.visibility = "visible";
            game.setGameState('help');
        }
        else {
            this.element.style.visibility = "hidden";
            game.setGameState('playing');
        }
        this.toggled = !this.toggled;
    }
};


