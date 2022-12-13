export const sidebar = {
    sidebarElement: document.getElementById("sidebar"),
    sidebarToggledButton: document.getElementById("sidebar_button_toggle"),
    sidebarToggled: false,
    addClickEvent() {
        // Get every button and add event;
        let sidebarTabs = this.sidebarElement.querySelectorAll("button");
        sidebarTabs.forEach((element) => {
            // Find first button and not append event to it
            if (element.previousElementSibling == null) {
                return;
            }
            element.addEventListener("click", (event) => this.showTab(true, element.innerText));
        });
        this.sidebarElement = document.getElementById("sidebar");
        document.getElementById("sidebar_button_toggle").addEventListener("click", () => this.toggleSidebar());
    },
    toggleSidebar() {
        if (this.sidebarToggled === false) {
            this.sidebarElement.style.visibility = "visible";
            this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.4 + "px";
            this.sidebarToggled = true;
            this.sidebarToggledButton.style.visibility = "visible";
            if (window.screen.width < 830) {
                this.sidebarToggledButton.style.cssFloat = "right";
            }
        }
        else {
            // Hide all bars
            sidebar.showTab(false, "");
            if (window.screen.width < 830) {
                this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.12 + "px";
            }
            else {
                this.sidebarElement.style.width = document.getElementsByTagName("aside")[0].clientWidth + "px";
            }
            this.sidebarToggled = false;
            if (window.screen.width > 830) {
                this.sidebarToggledButton.style.visibility = "hidden";
            }
            else {
                setTimeout(() => {
                    this.sidebarToggledButton.style.visibility = "hidden";
                    this.sidebarToggledButton.style.cssFloat = "left";
                }, 200);
            }
        }
    },
    showTab(sidebarCheck, buttonText) {
        if (this.sidebarToggled === false && sidebarCheck === true) {
            this.toggleSidebar();
        }
        let newActiveTab = buttonText;
        let tabs = document.getElementById("sidebar").querySelectorAll(".sidebar_tab");
        let tabNames = ["Adventure", "Countdowns", "Diplomacy", "Skills"];
        for (let i = 0; i < tabNames.length; i++) {
            let tab = tabs[i];
            if (newActiveTab.includes(tabNames[i])) {
                if (newActiveTab !== "Skills") {
                    // get_xp(false, false);
                }
                tab.style.visibility = "visible";
            }
            else {
                tab.style.visibility = "hidden";
            }
        }
    },
};
