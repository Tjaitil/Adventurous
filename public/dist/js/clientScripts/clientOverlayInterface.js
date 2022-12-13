import { itemTitle } from "../utilities/itemTitle.js";
import { selectItemEvent } from "../selectitem.js";
import { Game } from "../advclient.js";
import { inputHandler } from "./inputHandler.js";
import viewport from "./viewport.js";
import { jsUcWords } from "../utilities/uppercase.js";
export const clientOverlayInterface = {
    outerContainer: document.getElementById("news_content"),
    wrapper: document.getElementById("news_content_main_content"),
    sidePanel: document.getElementById("news_content_side_panel"),
    activeButton: null,
    interfacePageTitle: "",
    interfaceExitButton: null,
    setup() {
        this.interfaceExitButton = this.outerContainer.querySelectorAll(".cont_exit")[0];
        this.interfaceExitButton.addEventListener("click", () => this.hide());
    },
    loadingScreen() {
        let h = document.createElement("h1");
        h.innerText = "Loading...";
        h.id = "loading_message";
        this.show(h, false);
    },
    show(content, sidebar = true) {
        this.wrapper.innerHTML = "";
        this.outerContainer.style.top = viewport.elements.background.offsetTop + "px";
        document.getElementById("news").style.visibility = "visible";
        this.outerContainer.style.visibility = "visible";
        console.log(this.wrapper);
        if (typeof content == "object" && content instanceof HTMLElement) {
            if (content.innerText.indexOf("Loading") != -1) {
                this.wrapper.appendChild(content);
            }
            else if (Object.keys(content).length > 1) {
                for (let i = 0; i < Object.keys(content).length; i++) {
                    this.wrapper.appendChild(content[i]);
                }
            }
        }
        else {
            this.wrapper.innerHTML = "" + content;
        }
        if (sidebar == true) {
            this.createSidePanelTabs();
        }
    },
    hide() {
        if (this.interfacePageTitle !== undefined) {
            switch (this.interfacePageTitle) {
                case "merchant":
                    // TODO: Fix this
                    // updateStockCountdown("end");
                    break;
                case "tavern":
                    // TODO: Fix this
                    // let figures = document.getElementById("inventory").querySelectorAll("figure");
                    // figures.forEach((element) => element.removeEventListener("click", getHealingAmount));
                    break;
                default:
                    break;
            }
        }
        if (inputHandler.currentBuildingModule) {
            if (inputHandler.currentBuildingModule.default.onClose) {
                inputHandler.currentBuildingModule.default.onClose();
            }
        }
        this.outerContainer.innerHTML = "";
        this.outerContainer.style.visibility = "hidden";
        document.getElementById("news").style.visibility = "hidden";
        this.outerContainer.style.top = "200px";
        if (typeof Game.properties.inBuilding !== "undefined") {
            Game.properties.inBuilding = false;
        }
        if (selectItemEvent.selectItemStatus === true) {
            selectItemEvent.removeSelectEvent();
        }
        if (itemTitle.status === false) {
            itemTitle.addTitleEvent();
        }
    },
    createSidePanelTabs() {
        if (this.wrapper.style.width === "100%") {
            this.wrapper.style.width = "75%";
            this.sidePanel.style.width = "25%";
            this.sidePanel.style.display = "";
        }
        this.sidePanel.innerHTML = "";
        let divChildren = this.wrapper.children;
        // Exceptions that should not be rendered
        let exceptions = ["store-container-item-wrapper", "put_on", "persons", "stck_menu", "battle-result"];
        let buttonCount = 0;
        let divFirst = false;
        for (let i = 0; i < divChildren.length; i++) {
            let childElement = divChildren[i];
            if (childElement.tagName === "DIV" && exceptions.indexOf(childElement.id) == -1) {
                childElement.style.visibility = "hidden";
                childElement.style.display = "none";
                let button = document.createElement("button");
                // Get text from div id and remove underscore and Uppercase character of each word;
                let text = document.createTextNode(jsUcWords(underscoreTreatment(childElement.id, false)));
                button.appendChild(text);
                button.classList.add("building-tab");
                this.sidePanel.appendChild(button);
                if (divFirst === false) {
                    childElement.style.visibility = "visible";
                    divFirst = true;
                    this.showContent(undefined, button);
                    window.setTimeout(() => {
                        this.adjustWrapperHeight();
                    }, 500);
                }
                childElement.style.width = "100%";
                buttonCount++;
            }
        }
        if (buttonCount < 2) {
            // Remove padding
            this.wrapper.style.paddingRight = "0px";
            // If first div is persons then show second div ([2] in the child tree, [0] is title, [1] is persons div)
            // if (this.wrapper.querySelectorAll("div")[0].id == "persons") {
            // TODO: Fix this
            // news_content_main.children[2].style.visibility = "visible";
            // news_content_main.children[2].style.position = "";
            // document.getElementById("news_content_main_content").style.height =
            //     document.getElementById("news_content_main_content").querySelectorAll("div")[0].offsetHeight + 110 +
            //     news_content_main.children[2].offsetHeight + "px";
            // } else {
            // }
            console.log(this.wrapper.querySelectorAll("div"));
            this.wrapper.style.height = this.wrapper.querySelectorAll("div")[0].offsetHeight + 40 + "px";
            this.wrapper.querySelectorAll("div")[0].getBoundingClientRect();
            this.wrapper.style.width = "100%";
            this.sidePanel.style.display = "none";
        }
        else {
            this.addEventSidePanelTab();
            if (this.wrapper.style.paddingRight === "0") {
                this.wrapper.style.paddingRight = "8px";
            }
        }
    },
    showContent(event = undefined, selectedButton = undefined) {
        // Check if there is a selectedButton or if there is a DOM event
        if (selectedButton instanceof HTMLElement) {
            this.activeButton = selectedButton.innerText;
        }
        else {
            let eventTarget = event.target;
            this.activeButton = eventTarget.innerText;
        }
        console.log(this.activeButton);
        let buttons = document.getElementsByClassName("building-tab");
        for (let i = 0; i < buttons.length; i++) {
            let button = buttons[i];
            if (button.innerText == this.activeButton) {
                button.style.backgroundColor = "#474700";
                // document.getElementById(underscoreTreatment(button.innerText, true).toLowerCase()).style.position = "";
                document.getElementById(underscoreTreatment(button.innerText, true).toLowerCase()).style.visibility =
                    "visible";
                document.getElementById(underscoreTreatment(button.innerText, true).toLowerCase()).style.display =
                    "block";
            }
            else {
                button.style.backgroundColor = "";
                document.getElementById(underscoreTreatment(button.innerText, true).toLowerCase()).style.visibility =
                    "hidden";
                document.getElementById(underscoreTreatment(button.innerText, true).toLowerCase()).style.display =
                    "none";
            }
        }
        if (document.getElementById("form_cont") !== null &&
            document.getElementById("form_cont").style.display == "block") {
            document.getElementById("form_cont").style.display = "none";
        }
        this.adjustWrapperHeight();
    },
    adjustWrapperHeight() {
        this.wrapper.getBoundingClientRect();
        if (this.activeButton === "Overview") {
            let number = document.querySelectorAll(".warrior").length;
            this.wrapper.style.height =
                Math.ceil(number / 3) * 390 + 130 + "px";
        }
        else {
            let visibleChildren = [...this.wrapper.children];
            visibleChildren.filter((element) => element.style.visibility !== "hidden" && element.style.display !== "none");
            let totalHeight = 0;
            visibleChildren.forEach((element) => (totalHeight += element.offsetHeight));
            this.wrapper.style.height = totalHeight + "px";
        }
    },
    addEventSidePanelTab() {
        let buttons = [...document.getElementsByClassName("building-tab")];
        buttons.forEach((element) => {
            // Add eventListener to each node
            element.addEventListener("click", (event) => this.showContent(event));
        });
    },
    getInterfacePageTitle() {
        let title = document.getElementsByClassName("page_title")[0];
        if (title) {
            this.interfacePageTitle = title.innerText;
        }
    },
};
function underscoreTreatment(string, addUnderscore) {
    let result = string.search("_");
    let editedString;
    if (result != -1 && addUnderscore === false) {
        editedString = string.replace("_", " ");
    }
    else if (result == -1 && addUnderscore === true) {
        editedString = string.replace(" ", "_");
    }
    else {
        return string;
    }
    return editedString;
}
