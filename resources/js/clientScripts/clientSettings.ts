import { Game } from "../advclient";

export const clientSettings = {
    element: null,
    toggled: false,
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
    },
    init() {
        clientSettings.element = document.getElementById("client_settings_container");
        clientSettings.element.querySelectorAll(".cont_exit")[0].addEventListener("click", () => clientSettings.toggle());
        document.getElementById("setting_button").addEventListener("click", () => clientSettings.toggle());
        this.checkLocalStorage();
    },
    set(settingName: string) {
        let targetSetting = this.list.find(setting => setting.name === settingName);
        // Throw error if settingName doesn't exists
        if (!targetSetting) return false;

        switch (targetSetting.type) {
            case "switch":
                targetSetting.value = !targetSetting.value;
                break;
            default:
                break;
        }
        targetSetting.update();
    },
    checkLocalStorage() {
        // Loop through local storage to check if any settings is set
        this.list.forEach(setting => {
            let storedValue = localStorage.getItem(setting.name);
            // Pass storedValue if it exists
            if (storedValue) setting.setup(storedValue);
            else setting.setup();
        })
    },
    list: [
        {
            name: "minimalControls",
            type: 'switch',
            value: false,
            targetElement: null,
            setup(storedValue = null) {
                // Check for storedValue
                if (storedValue !== null) this.value = (storedValue != 'false') ? true : false;
                this.targetElement = document.getElementById("client-settings-minimal-control");
                this.targetElement.checked = this.value;
                this.targetElement.addEventListener("change", () => clientSettings.set("minimalControls"));
                this.update();
            },
            update() {
                let controlPara = [...document.querySelectorAll(".extendedControls")] as HTMLElement[];
                let style;
                if (this.value === true) {
                    style = "hidden";
                } else {
                    style = "visible";
                }
                controlPara.forEach(element => element.style.visibility = style);
                localStorage.setItem("minimalControls", this.value.toString());
            },
        },
    ]
}