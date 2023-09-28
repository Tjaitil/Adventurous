import { gameLogger } from "./gameLogger.js";
import { itemTitle } from "./itemTitle.js";
import { StoreSkillRequirements } from './StoreSkillRequirements.js';
import { SkillRequirementResource } from '../types/SkillRequirementResource';
import { ItemElement } from "./ItemElement.js";
import { itemPrices } from "../clientScripts/inventory.js";

const storeContainer: IStoreContainer = {
    storeItems: [],
    selectedTradeWrapper: null,
    selectedTradeAmount: null,
    selectedTradePriceElement: null,
    requirementsWrapper: null,
    itemInformationWrapper: null,
    skillRequirementsWrapper: null,
    doTradeButton: null,
    SelectedTradeContainer: null,
    noTradeSelectedWrapper: null,
    storeItemList: null,
    storeContainer: null,
    isBuying: true,
    maxAmount: 0,
    customButtonText: null,
    /**
     * @var onlySellStoreItems If true, the player can only sell items that are in the store
     */
    onlySellStoreItems: true,
    isTradeNotStoreItem: false,

    setOnlySellStoreItems(val: boolean): void {
        this.onlySellStoreItems = val;
    },

    init(): void {
        this.selectedTradeAmount = <HTMLInputElement>document.getElementById("store-container-selected-trade-amount");
        this.selectedTradeWrapper = document.getElementById("store-container-selected-trade");
        this.selectedTradePriceElement = document.getElementById("store-container-trade-price-span");
        this.requirementsWrapper = document.getElementById("store-container-item-requirements");
        this.itemInformationWrapper = document.getElementById("store-container-item-information");
        this.skillRequirementsWrapper = document.querySelectorAll("#store-container-item-selected .skill-requirements")[0] as HTMLElement;
        if (document.getElementById("store-container-item-event")) {
            this.doTradeButton = <HTMLButtonElement>document.getElementById("store-container-item-event");
        } else {
            this.doTradeButton = <HTMLButtonElement>document.getElementById("store-container-item-trade-button");
        }
        this.SelectedTradeContainer = document.getElementById("store-container-do-trade");
        this.noTradeSelectedWrapper = document.getElementById("store-container-no-trade-selected");
        this.storeItemList = document.getElementById("store-container-item-list");
        this.storeContainer = document.getElementById("store-container-item-wrapper");
    },

    adjustStoreItemListHeight(): void {
        let height = this.storeContainer.clientHeight;
        this.storeItemList.style.maxHeight = height + "px";
    },

    setStoreItems(items: StoreItemResource[]) {
        this.storeItems = items;
    },

    setNewStoreItemsUI(html: string): void {
        let div = document.createElement("div");
        div.innerHTML = html;

        let list = div.firstChild;
        this.storeItemList.replaceWith(list);
        this.addSelectTrade();
    },

    setTradeButtonText(text: string): void {
        this.doTradeButton.innerHTML = text;
    },

    addSelectedItemButtonEvent(func: CallableFunction, text?: string): void {
        this.doTradeButton.addEventListener("click", () => func());
        // Custom text for button
        this.customButtonText = text;
        if (this.customButtonText) this.setTradeButtonText(this.customButtonText);
    },

    /**
     * Add select event to items in store list
     */
    addSelectTrade(): void {
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => this.selectTrade(event)));
    },

    /**
     * Add select event to inventory items
     */
    addSelectTradeToInventory(): void {
        [...document.getElementsByClassName("inventory_item")].forEach(element =>
            element.addEventListener("click", event => this.selectInventoryTrade(event)));
    },

    /**
     * Reset selected trade UI
     */
    resetUI(): void {
        itemTitle.resetItemTooltip();
        this.SelectedTradeContainer.style.display = "flex";
        this.noTradeSelectedWrapper.style.display = "none";
        this.selectedTradeWrapper.innerHTML = "";
        this.selectedTradePriceElement.innerHTML = "";
        this.selectedTradeAmount.value = "1";
        this.doTradeButton.disabled = false;
        if (this.requirementsWrapper) {
            this.requirementsWrapper.innerHTML = "";
        }
        if (this.itemInformationWrapper) {
            this.itemInformationWrapper.innerHTML = "";
        }
        if (this.skillRequirementsWrapper) {
            this.skillRequirementsWrapper.innerHTML = "";
        }
    },

    findItemData(item: string): StoreItemResource {
        return this.storeItems.find((element) => element.name === item);
    },

    selectTrade(event: Event): void {
        this.resetUI();
        let eventElement = <HTMLElement>event.currentTarget;
        let elementDiv = eventElement.closest(".store-container-item");

        let itemElement = new ItemElement(elementDiv.querySelectorAll(".item")[0].cloneNode(true), undefined, {
            showTooltip: true
        });

        let itemData = this.findItemData(itemElement.item);

        let price = itemData.adjusted_store_value
            ? itemData.adjusted_store_value :
            itemData.store_value;
        this.isBuying = true;
        this.setTradeButtonText(this.customButtonText ? this.customButtonText : "Buy");

        let item = itemElement.item;
        this.maxAmount = itemElement.amount;

        this.setSelectedTrade(price, itemElement.HTMLElement);
        this.checkHasRequirements(item);
        this.checkItemMultiplier(itemData);
        this.checkSkillRequirement(itemData);
        this.checkItemHasInformation(itemData);
    },

    selectInventoryTrade(event: Event): void {
        this.resetUI();
        this.isBuying = false;
        let eventElement = <HTMLElement>event.currentTarget;
        let elementDiv;

        let itemData;
        let price;
        elementDiv = eventElement.closest(".inventory_item");
        this.isBuying = false;
        this.setTradeButtonText("Sell");

        let itemElement = new ItemElement(elementDiv.cloneNode(true), undefined, {
            showTooltip: true
        });
        itemElement.setClass("item").removeClass("inventory_item");
        itemData = this.findItemData(itemElement.item);

        if (this.onlySellStoreItems) {
            if (!itemData) {
                gameLogger.addMessage("You can't sell that item here", true);
            }
            price = itemData.store_buy_price;
            this.checkHasRequirements(itemElement.item);
            this.checkItemMultiplier(itemData);
            this.checkSkillRequirement(itemData);
            this.checkItemHasInformation(itemData);
        } else {
            if (itemData) {
                price = itemData.store_buy_price;
            } else {
                price = itemPrices.findItem(itemElement.item);
                this.isTradeNotStoreItem = true;
            }
        }

        this.setSelectedTrade(price, itemElement.HTMLElement);
    },

    checkHasRequirements(item: string): void {
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

    setSelectedTrade(price: number, element: HTMLElement): void {
        this.selectedTradeWrapper.appendChild(element);
        this.selectedTradePriceElement.innerHTML = price + " ";

        let nameElement = <HTMLElement>this.selectedTradeWrapper.querySelectorAll("figcaption")[0];
        nameElement.classList.remove("tooltip");
        let imageElement = <HTMLImageElement>this.selectedTradeWrapper.querySelectorAll("img")[0];
        imageElement.classList.add("mx-auto");
        let itemAmountElement = <HTMLInputElement>this.selectedTradeWrapper
            .querySelectorAll(".item_amount")[0];
        // Hide item amount on selectd item by default
        if (itemAmountElement) {
            itemAmountElement
                .style.visibility = "hidden";
        }

    },

    getSelectedTrade(): { item: string, amount: number, isBuying: boolean } | false {
        let item = this.selectedTradeWrapper
            .querySelectorAll("figcaption")[0]
            .innerHTML
            .toLowerCase()
            .trim();

        let amount = parseInt(this.selectedTradeAmount.value);

        if (this.maxAmount !== -1 && amount > this.maxAmount && this.isBuying) {
            gameLogger.addMessage("You can't buy that many", true);
            return false;
        } else if (amount === 0) {
            gameLogger.addMessage("Please enter a valid quantity", true);
            return false;
        } else if (!item) {
            gameLogger.addMessage("Please select an item", true);
        }

        return {
            item,
            amount,
            isBuying: this.isBuying
        }
    },

    checkSkillRequirement(item: StoreItemResource): void {
        if (item.skill_requirements.length > 0) {
            let skillRequirements = new StoreSkillRequirements(this.skillRequirementsWrapper, item.skill_requirements);
            skillRequirements.clearContainer();
            skillRequirements.generateContainer();
        }
    },

    addRequirementEvent(funcName: CallableFunction): void {
        if (!funcName && funcName.length === 0) return;
        [...document.getElementsByClassName("store-container-item")].forEach(element =>
            element.addEventListener("click", event => funcName(event)));

    },

    checkItemHasInformation(itemData: StoreItemResource): void {
        if (itemData.information) {
            this.itemInformationWrapper.innerHTML = itemData.information
        }
    },

    /** 
     * Check if item creates a set amount  
     */
    checkItemMultiplier(itemData: StoreItemResource): void {
        if (itemData.item_multiplier > 1) {
            let span = document.createElement("span")

            span.classList.add("item_amount");
            span.innerHTML = "" + itemData.item_multiplier;
            span.style.visibility = "visible";

            this.selectedTradeWrapper.appendChild(span);
        }
    },

    /**
     * Check if tooltip is present in storecontainer and reset it
     */
    checkItemTooltip(): void {
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
    store_buy_price: number;
    required_items: StoreItemResource[];
    item_multiplier: number;
    adjusted_store_value: number;
    adjusted_difference: number;
    skill_requirements: SkillRequirementResource[]
    information: string;
}
interface IStoreContainer {
    storeItems: StoreItemResource[];
    selectedTradeWrapper: HTMLElement;
    selectedTradeAmount: HTMLInputElement;
    selectedTradePriceElement: HTMLElement;
    requirementsWrapper: HTMLElement;
    itemInformationWrapper: HTMLElement;
    skillRequirementsWrapper: HTMLElement;
    doTradeButton: HTMLButtonElement;
    SelectedTradeContainer: HTMLElement;
    noTradeSelectedWrapper: HTMLElement;
    storeItemList: HTMLElement;
    storeContainer: HTMLElement;
    isBuying: boolean;
    maxAmount: number;
    customButtonText: string;
    onlySellStoreItems: boolean;
    isTradeNotStoreItem: boolean;
    setOnlySellStoreItems(val: boolean): void;
    init(): void;
    adjustStoreItemListHeight(): void;
    setStoreItems(items: StoreItemResource[]): void;
    setNewStoreItemsUI(html: string): void;
    setTradeButtonText(text: string): void;
    addSelectedItemButtonEvent(func: CallableFunction, text?: string): void;
    addSelectTrade(): void;
    addSelectTradeToInventory(): void;
    resetUI(): void;
    findItemData(item: string): StoreItemResource;
    selectTrade(event: Event): void;
    selectInventoryTrade(event: Event): void;
    checkHasRequirements(item: string): void;
    setSelectedTrade(price: number, element: HTMLElement): void;
    getSelectedTrade(): { item: string, amount: number, isBuying: boolean } | false;
    checkSkillRequirement(item: StoreItemResource): void;
    addRequirementEvent(funcName: CallableFunction): void;
    checkItemHasInformation(itemData: StoreItemResource): void;
    checkItemMultiplier(itemData: StoreItemResource): void;
    checkItemTooltip(): void;
}