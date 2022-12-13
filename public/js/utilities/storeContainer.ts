import { checkInventoryStatus } from "../clientScripts/inventory";
import { commonMessages, gameLogger } from "./gameLogger";
import { itemTitle } from "./itemTitle";
import { jsUcWords } from "./uppercase";

const storeContainer = {
    addSelectedItemButtonEvent(func: CallableFunction, text: string) {
        document.getElementById("store-container-item-event-button").addEventListener("click", () => func());
        // Custom text for button
        if (text) document.getElementById("store-container-item-event-button").innerHTML = text;
    },
    addSelectTrade() {
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => this.selectTrade(event)));
    },
    selectTrade(event: Event) {
        document.getElementById("store-container-do-trade").querySelectorAll("button")[0].disabled = false;

        let eventElement = <HTMLElement>event.currentTarget;
        let elementDiv = eventElement.closest(".store-container-item");

        let price = elementDiv.querySelectorAll(".store-container-item-price")[0].innerHTML.trim();
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.trim();
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);

        document.getElementById("store-container-selected-trade").innerHTML = "";
        document.getElementById("store-container-selected-trade").appendChild(figure);
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerHTML = "" + 0;
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerHTML = price;

        let itemAmountElement = <HTMLInputElement>document.getElementById("store-container-selected-trade")
            .querySelectorAll(".item_amount")[0];
        // Hide item amount on selectd item by default
        if (itemAmountElement) {
            itemAmountElement
                .style.visibility = "none";
        }

    },
    getSelectedTrade() {
        if (checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        let item = document.getElementById("store-container-selected-trade")
            .querySelectorAll("figcaption")[0]
            .innerHTML
            .toLowerCase()
            .trim();

        let amountElement = <HTMLInputElement>document.getElementById("store-container-selected-trade-amount");
        let amount = parseInt(amountElement.value);

        if (amount === 0) {
            gameLogger.addMessage("Please enter a valid quantity", true);
            return false;
        } else if (!item) {
            gameLogger.addMessage("Please select an item", true);
        }

        return {
            item,
            amount
        }
    },
    clearRequirementContainer() {
        this.checkItemTooltip();
        document.getElementById("store-container-item-requirements").innerHTML = "";
    },
    addRequirementEvent(funcName: CallableFunction) {
        if (!funcName && funcName.length === 0) return false;
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => funcName(event)));
    },
    addRequirement(name: string, amount: number, imgSrc: string) {
        // Add requirements to storeContainer
        // Create div
        let div = document.createElement("div");
        div.classList.add("item");

        let figure = document.createElement("figure");
        figure.appendChild(document.createElement("img"));

        let figcaption = document.createElement("figcaption");
        figcaption.classList.add("tooltip");
        figure.appendChild(figcaption);
        div.appendChild(figure);

        let span = document.createElement("span");
        span.classList.add("item_amount");
        div.appendChild(span);
        div.querySelectorAll("figcaption")[0].innerHTML = jsUcWords(name);
        div.querySelectorAll("img")[0].src = "public/images/" + imgSrc + ".png";
        div.querySelectorAll(".item_amount")[0].innerHTML = "" + amount;

        // Add itemtitle events
        div.addEventListener("mouseenter", (event) => itemTitle.show(event));
        div.addEventListener("mouseleave", () => itemTitle.hide());
        document.getElementById("store-container-item-requirements").append(div);
    },

    // TODO: Fix this
    /** Check if item creates a set amount  */
    checkSetAmount(itemData: { 'setAmount': number }) {
        if (itemData.setAmount) {
            let span = document.createElement("span")

            span.classList.add("item_amount");
            span.innerHTML = "" + itemData.setAmount;
            span.style.visibility = "visible";

            document.getElementById("store-container-selected-trade").appendChild(span);
        }
    },
    checkItemTooltip() {
        if (document.getElementById("news_content_main_content").querySelectorAll("#item_tooltip").length > 0) {
            itemTitle.resetItemTooltip();
        }
    }
}
export default storeContainer;