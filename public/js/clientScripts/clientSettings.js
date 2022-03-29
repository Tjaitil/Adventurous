window.addEventListener('load', function() {  
    clientSettings.element = document.getElementById("client_settings_container");
    clientSettings.element.querySelectorAll(".cont_exit")[0].addEventListener("click", () => clientSettings.toggle());
    document.getElementById("setting_button").addEventListener("click", () => clientSettings.toggle());
    clientSettings.init();
}); 

const clientSettings = {
    element: null,
    toggled: false,
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
    },
    init() {
        // Wait for DOM content to load before adding targetElement 
        this.minimalControls.targetElement = document.getElementById("client-settings-minimal-control");
        document.getElementById("client-settings-minimal-control").addEventListener("change", () => this.set('minimalControls'));
    },
    set(settingName) {
        console.log(this[settingName]);
        if(!this[settingName]) return false;
        let targetSetting = this[settingName];
        if(targetSetting.type === "switch") {
            targetSetting.value = !targetSetting.value;
        }
        targetSetting.update();
    },
    checkLocalStorage() {
        if(localStorage.getItem('minimalControls')) this.minimalControls.value = localStorage.getItem('minimalControls');
    },
    minimalControls: {
        type: 'switch',
        value: false,
        update() {
            let controlPara = document.querySelectorAll(".extendedControls");
            let style;
            if(this.value === true) {
                style = "none";
            } else {
                style = "block";
            }
            controlPara.forEach(element => element.style.display = style);
        },
        targetElement: null,
    },
}