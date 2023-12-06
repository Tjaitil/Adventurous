import { ItemPricesResponse } from './../types/Responses/PricesResponse';
import { ItemSelector } from '../ItemSelector';
import { itemTitle } from '../utilities/itemTitle';
import { inputHandler } from './inputHandler';
import { getClientPageTitle } from '../utilities/getClientPageTitle';
import { AdvApi } from '../AdvApi';
import { CustomFetchApi } from '../CustomFetchApi';

/**
 * @deprecated
 */
export async function getInventory() {
    AdvApi.get("/inventory")
        .then(data => {
            document.getElementById("inventory").innerHTML = data["html"]["inventory"];

            if (getClientPageTitle() == "Stockpile") {
                let figures = document.getElementById("inventory").querySelectorAll("figure");
                figures.forEach(element =>
                    element.addEventListener('click', inputHandler.currentBuildingModule.show_menu)
                );
                itemTitle.removeTitleEvent();
            }
            else {
                itemTitle.addTitleEvent();
            }
            itemPrices.get();
            if (ItemSelector.isEventSet) {
                ItemSelector.addSelectEventToInventory();
            }
            // document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
        })
}

/**
 * 
 * @deprecated
 */
export async function updateInventory(page = false, addSelect = false) {
    await fetch("handlers/handlerf.php?file=inventory")
        .then(response => response.text())
        .then(data => {
            document.getElementById("inventory").innerHTML = data;

            if (getClientPageTitle() == "Stockpile") {
                let figures = document.getElementById("inventory").querySelectorAll("figure");
                figures.forEach(element =>
                    element.addEventListener('click', inputHandler.currentBuildingModule.show_menu)
                );
                itemTitle.removeTitleEvent();
            }
            else {
                itemTitle.addTitleEvent();
            }
            itemPrices.get();
            if (ItemSelector.isEventSet) {
                ItemSelector.addSelectEventToInventory();
            }
        })
}

/**
 * 
 * @deprecated
 */
export function checkInventoryStatus() {
    // Fetch items amount
    let items = document.getElementsByClassName("inventory_item");

    let status = (items.length === 18);

    let inventoryStatusElement = document.getElementById("inventory-status");
    // Adjust color according to inventory status
    if (status) {
        inventoryStatusElement.classList.add("not-able-color");
    } else {
        inventoryStatusElement.classList.remove("not-able-color");
    }
    return status;
}


export class Inventory {
    private static itemsElements: Element[];
    private static itemsAmount: number;
    private static isInited: boolean = false;

    static init() {
        if (this.isInited) return;
        this.itemsElements = [...document.querySelectorAll(".inventory_item")];
        this.itemsAmount = this.itemsElements.length;
        this.isInited = true;
    }

    static async update() {
        return CustomFetchApi.get<response>("/inventory")
            .then(data => {
                document.getElementById("inventory").innerHTML = data["html"]["inventory"];
                this.itemsElements = [...document.querySelectorAll(".inventory_item")];
                this.itemsAmount = this.itemsElements.length;
                this.isFull() ? this.styleSpaceIndicator("full") : this.styleSpaceIndicator("");

                itemTitle.addTitleEvent();
                itemPrices.get();

                if (ItemSelector.isEventSet) {
                    ItemSelector.addSelectEventToInventory();
                }
            })
    }

    static isFull() {
        return this.itemsAmount === 18;
    }

    static styleSpaceIndicator(status: "full" | "") {
        let inventoryStatusElement = document.getElementById("inventory-status");
        // Adjust color according to inventory status
        if (status === "full") {
            inventoryStatusElement.classList.add("not-able-color");
        } else {
            inventoryStatusElement.classList.remove("not-able-color");
        }
    }

    static get items() {
        return this.itemsElements;
    }
}

interface postData {
    foo: string;
}
interface response {
    html: any;
}

interface ItemPrice {
    name: string;
    store_value: number;
}
export const itemPrices = {
    prices: <ItemPrice[]>[],
    findItem(itemName) {
        let item = itemName.toLowerCase();

        let array = this.prices.filter((element) => {
            if (element.name === item) return element.store_value;
        });
        if (array.length > 0) {
            return array[0].store_value;
        }
        else {
            return "N/A";
        }
    },
    get() {
        CustomFetchApi.get<ItemPricesResponse>("/inventory/prices").then(response => {
            this.prices = response.prices;
        }).catch(() => false);
    }
}