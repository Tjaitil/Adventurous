import { gameLogger } from "./gameLogger.js";
import { itemTitle } from "./itemTitle.js";
import { StoreSkillRequirements } from './StoreSkillRequirements.js';
import { SkillRequirementResource } from '../types/SkillRequirementResource';
import { ItemElement } from "./ItemElement.js";

const storeContainer = {
    storeItems: [] as StoreItemResource[],
    selectedTradeWrapper: HTMLElement = null,
    requirementsWrapper: HTMLElement = null,
    itemInformationWrapper: HTMLElement = null,
    skillRequirementsWrapper: HTMLElement = null,
    doTradeButton: HTMLButtonElement = null,
    SelectedTradeContainer: HTMLElement = null,
    noTradeSelectedWrapper: HTMLElement = null,
    storeItemList: HTMLElement = null,
    storeContainer: HTMLElement = null,

    init() {
        this.selectedTradeWrapper = document.getElementById("store-container-selected-trade");
        this.requirementsWrapper = document.getElementById("store-container-item-requirements");
        this.itemInformationWrapper = document.getElementById("store-container-item-information");
        this.skillRequirementsWrapper = document.querySelectorAll("#store-container-item-selected .skill-requirements")[0] as HTMLElement;
        this.doTradeButton = document.getElementById("store-container-item-event-button") as HTMLButtonElement;
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

    setStoreItems(items: StoreItemResource[]) {
        this.storeItems = items;
    },

    addSelectedItemButtonEvent(func: CallableFunction, text: string) {
        this.doTradeButton.addEventListener("click", () => func());
        // Custom text for button
        if (text) this.doTradeButton.innerHTML = text;
    },

    addSelectTrade() {
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => this.selectTrade(event)));
    },

    selectTrade(event: Event) {
        itemTitle.resetItemTooltip();
        this.SelectedTradeContainer.style.display = "flex";
        this.noTradeSelectedWrapper.style.display = "none";
        this.selectedTradeWrapper.innerHTML = "";
        this.doTradeButton.disabled = false;
        this.requirementsWrapper.innerHTML = "";
        this.itemInformationWrapper.innerHTML = "";
        this.skillRequirementsWrapper.innerHTML = "";

        let eventElement = <HTMLElement>event.currentTarget;
        let elementDiv = eventElement.closest(".store-container-item");


        let itemElement = new ItemElement(elementDiv.querySelectorAll(".item")[0].cloneNode(true), undefined, {
            showTooltip: true
        });

        let item = itemElement.item;
        let itemData = this.storeItems.find((element) => element.name === item);

        let price = itemData.adjusted_store_value
            ? itemData.adjusted_store_value :
            itemData.store_value;

        elementDiv.querySelectorAll(".store-container-item-price")[0].innerHTML.trim();
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);

        this.selectedTradeWrapper.appendChild(itemElement.HTMLElement);
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerHTML = price + " ";

        let itemAmountElement = <HTMLInputElement>this.selectedTradeWrapper
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

    checkHasRequirements(item: string) {
        let itemData = this.storeItems.find((element) => element.name === item);
        if (itemData && itemData.required_items.length > 0) {
            itemData.required_items.forEach((element) => {

                let requirement = new ItemElement(undefined, {
                    amount: element.amount,
                    name: element.name,
                    className: "store-container-item-requirement"
                });
                this.requirementsWrapper.appendChild(requirement.HTMLElement);
            });
        }
    },

    getSelectedTrade(): { item: string, amount: number } | false {

        let item = this.selectedTradeWrapper
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

    checkSkillRequirement(item: StoreItemResource) {
        if (item.skill_requirements.length > 0) {
            let skillRequirementsWrapper = document.getElementsByClassName("skill-requirements")[0] as HTMLElement;
            let skillRequirements = new StoreSkillRequirements(skillRequirementsWrapper, item.skill_requirements);
            skillRequirements.clearContainer();
            skillRequirements.generateContainer();
        }
    },

    addRequirementEvent(funcName: CallableFunction) {
        if (!funcName && funcName.length === 0) return false;
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => funcName(event)));

    },

    checkItemHasInformation(itemData: StoreItemResource) {
        if (itemData.information) {
            this.itemInformationWrapper.innerHTML = itemData.information
        }
    },

    /** Check if item creates a set amount  */
    checkItemMultiplier(itemData: StoreItemResource) {
        if (itemData.item_multiplier > 1) {
            let span = document.createElement("span")

            span.classList.add("item_amount");
            span.innerHTML = "" + itemData.item_multiplier;
            span.style.visibility = "visible";

            this.selectedTradeWrapper.appendChild(span);
        }
    },

    checkItemTooltip() {
        if (document.getElementById("news_content_main_content").querySelectorAll("#item_tooltip").length > 0) {
            itemTitle.resetItemTooltip();
        }
    }
}
export default storeContainer;

export interface StoreItemResource {
    name: string;
    amount: number
    store_value: number;
    sell_value: number;
    required_items: StoreItemResource[];
    item_multiplier: number;
    adjusted_store_value: number;
    adjusted_difference: number;
    skill_requirements: SkillRequirementResource[]
    information: string;
}