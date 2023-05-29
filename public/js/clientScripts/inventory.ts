import { ItemPricesResponse } from './../types/responses/PricesResponse';
import { ItemSelector } from '../ItemSelector.js';
import { itemTitle } from '../utilities/itemTitle.js';
import { inputHandler } from './inputHandler.js';
import { getClientPageTitle } from '../utilities/getClientPageTitle.js';
import { AdvApi } from '../AdvApi.js';

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
    static items: HTMLElement[];

    static update() {
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
            })
    }



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
        AdvApi.get<ItemPricesResponse>("/inventory/prices").then(response => {
            this.prices = response.prices;
        }).catch(() => false);
    }
}