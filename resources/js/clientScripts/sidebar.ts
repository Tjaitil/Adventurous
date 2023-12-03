import { hideAllSkillTooltips } from "./getXp";

export const sidebar = {
    sidebarElement: document.getElementById("sidebar"),
    sidebarToggledButton: document.getElementById("sidebar_button_toggle"),
    isSidebarToggled: false,
    currentTab: null,
    adjustSidebar() {
        let parent = this.sidebarElement.parentElement;
        this.sidebarElement.style.maxWidth = parent.clientWidth + "px";
    },
    addClickEvent() {
        this.sidebarToggledButton.addEventListener("click", () => this.toggleSidebar());
        let sidebarTabs = <HTMLElement[]>[...this.sidebarElement.querySelectorAll(".sidebar-tab")];
        sidebarTabs.forEach((element, index) => {
            if (index === 0) {
                this.currentTab = element.getAttribute("id");
            }
            element.addEventListener("click", (event) => this.showTab(event));
        });
        this.sidebarElement = document.getElementById("sidebar");
    },
    toggleSidebar() {
        if (this.isSidebarToggled === false) {
            this.sidebarElement.style.visibility = "visible";
            this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.4 + "px";
            this.isSidebarToggled = true;
            this.sidebarToggledButton.style.visibility = "visible";
            if (window.screen.width < 830) {
                this.sidebarToggledButton.style.cssFloat = "right";
            }
        } else {
            this.hideTabs();
            if (window.screen.width < 830) {
                this.adjustSidebar();
            } else {
            }
            this.isSidebarToggled = false;
            if (window.screen.width > 830) {
                this.sidebarToggledButton.style.visibility = "hidden";
            } else {
                setTimeout(() => {
                    this.sidebarToggledButton.style.visibility = "hidden";
                    this.sidebarToggledButton.style.cssFloat = "left";
                }, 200);
            }
        }
    },
    showTab(event: MouseEvent) {
        let element = <HTMLElement>event.currentTarget;
        if (this.isSidebarToggled === false) {
            this.toggleSidebar();
        }

        let targetTabPanelID = element.getAttribute("aria-controls");
        if (this.currentTab === targetTabPanelID) return;

        if (!targetTabPanelID) return

        let tabpanels = <HTMLElement[]>[...document.getElementById("sidebar-tabpanels").querySelectorAll(".tabpanel")];
        tabpanels.forEach(element => {
            if (element.id === targetTabPanelID) {
                element.classList.remove("hidden");
            } else {
                element.classList.add("hidden");
            }
        });
        hideAllSkillTooltips();
    },
    hideTabs() {
        let tabpanels = <HTMLElement[]>[...document.getElementById("sidebar-tabpanels").querySelectorAll(".tabpanel")];
        tabpanels.forEach(element => {
            element.style.visibility = "hidden";
        });
    }
};
window.addEventListener("resize", () => sidebar.adjustSidebar());
sidebar.adjustSidebar();
