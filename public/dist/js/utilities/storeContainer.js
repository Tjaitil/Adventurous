import { checkInventoryStatus } from "../clientScripts/inventory.js";
import { commonMessages, gameLogger } from "./gameLogger.js";
import { itemTitle } from "./itemTitle.js";
import { jsUcWords } from "./uppercase.js";
import { StoreSkillRequirements } from './StoreSkillRequirements.js';
const storeContainer = {
    storeItems: [],
    selectedTadeWrapper: HTMLElement = null,
    requirementsWrapper: HTMLElement = null,
    itemInformationWrapper: HTMLElement = null,
    skillRequirementsWrapper: HTMLElement = null,
    doTradeButton: HTMLButtonElement = null,
    SelectedTradeContainer: HTMLElement = null,
    noTradeSelectedWrapper: HTMLElement = null,
    storeItemList: HTMLElement = null,
    storeContainer: HTMLElement = null,
    init() {
        this.selectedTadeWrapper = document.getElementById("store-container-selected-trade");
        this.requirementsWrapper = document.getElementById("store-container-item-requirements");
        this.itemInformationWrapper = document.getElementById("store-container-item-information");
        this.skillRequirementsWrapper = document.querySelectorAll("#store-container-item-selected .skill-requirements")[0];
        this.doTradeButton = document.getElementById("store-container-item-event-button");
        this.SelectedTradeContainer = document.getElementById("store-container-do-trade");
        this.noTradeSelectedWrapper = document.getElementById("store-container-no-trade-selected");
        this.storeItemList = document.getElementById("store-container-item-list");
        this.storeContainer = document.getElementById("store-container-item-wrapper");
        this.adjustStoreItemListHeight();
    },
    adjustStoreItemListHeight() {
        let height = this.storeContainer.clientHeight;
        this.storeItemList.style.maxHeight = height + "px";
    },
    setStoreItems(items) {
        this.storeItems = items;
    },
    addSelectedItemButtonEvent(func, text) {
        this.doTradeButton.addEventListener("click", () => func());
        // Custom text for button
        if (text)
            this.doTradeButton.innerHTML = text;
    },
    addSelectTrade() {
        [...document.getElementsByClassName("store-container-item")].forEach(element => element.addEventListener("click", event => this.selectTrade(event)));
    },
    selectTrade(event) {
        itemTitle.resetItemTooltip();
        this.SelectedTradeContainer.style.display = "block";
        this.noTradeSelectedWrapper.style.display = "none";
        this.selectedTadeWrapper.innerHTML = "";
        this.doTradeButton.disabled = false;
        this.requirementsWrapper.innerHTML = "";
        this.itemInformationWrapper.innerHTML = "";
        this.skillRequirementsWrapper.innerHTML = "";
        let eventElement = event.currentTarget;
        let elementDiv = eventElement.closest(".store-container-item");
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.trim().toLowerCase();
        let itemData = this.storeItems.find((element) => element.name === item);
        let price = itemData.adjusted_store_value
            ? itemData.adjusted_store_value :
            itemData.store_value;
        elementDiv.querySelectorAll(".store-container-item-price")[0].innerHTML.trim();
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("store-container-selected-trade").appendChild(figure);
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerHTML = price + " ";
        let itemAmountElement = this.selectedTadeWrapper
            .querySelectorAll(".item_amount")[0];
        // Hide item amount on selectd item by default
        if (itemAmountElement) {
            itemAmountElement
                .style.visibility = "none";
        }
        this.checkHasRequirements(item);
        this.checkItemMultiplier(itemData);
        this.checkSkillRequirement(itemData);
        this.checkItemHasInformation(itemData);
    },
    checkHasRequirements(item) {
        let itemData = this.storeItems.find((element) => element.name === item);
        if (itemData && itemData.required_items.length > 0) {
            this.clearRequirementContainer();
            itemData.required_items.forEach((element) => this.addRequirement(element.name, element.amount, element.name));
        }
    },
    getSelectedTrade() {
        if (checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let item = this.selectedTadeWrapper
            .querySelectorAll("figcaption")[0]
            .innerHTML
            .toLowerCase()
            .trim();
        let amountElement = document.getElementById("store-container-selected-trade-amount");
        let amount = parseInt(amountElement.value);
        if (amount === 0) {
            gameLogger.addMessage("Please enter a valid quantity", true);
            return false;
        }
        else if (!item) {
            gameLogger.addMessage("Please select an item", true);
        }
        return {
            item,
            amount
        };
    },
    checkSkillRequirement(item) {
        if (item.skill_requirements.length > 0) {
            let skillRequirementsWrapper = document.getElementsByClassName("skill-requirements")[0];
            let skillRequirements = new StoreSkillRequirements(skillRequirementsWrapper, item.skill_requirements);
            skillRequirements.clearContainer();
            skillRequirements.generateContainer();
        }
    },
    clearRequirementContainer() {
    },
    addRequirementEvent(funcName) {
        if (!funcName && funcName.length === 0)
            return false;
        [...document.getElementsByClassName("store-container-item")].forEach(element => element.addEventListener("click", event => funcName(event)));
    },
    addRequirement(name, amount, imgSrc) {
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
        this.requirementsWrapper.appendChild(div);
    },
    checkItemHasInformation(itemData) {
        if (itemData.information) {
            this.itemInformationWrapper.innerHTML = itemData.information;
        }
    },
    /** Check if item creates a set amount  */
    checkItemMultiplier(itemData) {
        if (itemData.item_multiplier > 1) {
            let span = document.createElement("span");
            span.classList.add("item_amount");
            span.innerHTML = "" + itemData.item_multiplier;
            span.style.visibility = "visible";
            this.selectedTadeWrapper.appendChild(span);
        }
    },
    checkItemTooltip() {
        if (document.getElementById("news_content_main_content").querySelectorAll("#item_tooltip").length > 0) {
            itemTitle.resetItemTooltip();
        }
    }
};
export default storeContainer;
