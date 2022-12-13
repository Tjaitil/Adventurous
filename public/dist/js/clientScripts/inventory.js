import { selectItemEvent } from '../selectitem.js';
import { ajaxG } from '../ajax.js';
import { itemTitle } from '../utilities/itemTitle.js';
import { inputHandler } from './inputHandler.js';
import { getClientPageTitle } from '../utilities/getClientPageTitle.js';
import { AdvApi } from '../AdvApi.js';
export async function getInventory() {
    AdvApi.get("/inventory")
        .then(data => {
        document.getElementById("inventory").innerHTML = data["html"]["inventory"];
        if (getClientPageTitle() == "Stockpile") {
            let figures = document.getElementById("inventory").querySelectorAll("figure");
            figures.forEach(element => element.addEventListener('click', inputHandler.currentBuildingModule.show_menu));
            itemTitle.removeTitleEvent();
        }
        else {
            itemTitle.addTitleEvent();
        }
        itemPrices.get();
        if (selectItemEvent.selectItemStatus == true) {
            selectItemEvent.addSelectEvent();
        }
        // document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
    });
}
export async function updateInventory(page = false, addSelect = false) {
    await fetch("handlers/handlerf.php?file=inventory")
        .then(response => response.text())
        .then(data => {
        document.getElementById("inventory").innerHTML = data;
        if (getClientPageTitle() == "Stockpile") {
            let figures = document.getElementById("inventory").querySelectorAll("figure");
            figures.forEach(element => element.addEventListener('click', inputHandler.currentBuildingModule.show_menu));
            itemTitle.removeTitleEvent();
        }
        else {
            itemTitle.addTitleEvent();
        }
        itemPrices.get();
        if (selectItemEvent.selectItemStatus == true) {
            selectItemEvent.addSelectEvent();
        }
        // document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
    });
}
export function checkInventoryStatus() {
    // Fetch items amount
    let items = document.getElementsByClassName("inventory_item");
    let status = (items.length === 18);
    let inventoryStatusElement = document.getElementById("inventory-status");
    // Adjust color according to inventory status
    if (status) {
        inventoryStatusElement.classList.add("not-able-color");
    }
    else {
        inventoryStatusElement.classList.remove("not-able-color");
    }
    return status;
}
export class Inventory {
    static items;
    static update() {
        AdvApi.get("/inventory")
            .then(data => {
            document.getElementById("inventory").innerHTML = data["html"]["inventory"];
            if (getClientPageTitle() == "Stockpile") {
                let figures = document.getElementById("inventory").querySelectorAll("figure");
                figures.forEach(element => element.addEventListener('click', inputHandler.currentBuildingModule.show_menu));
                itemTitle.removeTitleEvent();
            }
            else {
                itemTitle.addTitleEvent();
            }
            itemPrices.get();
            if (selectItemEvent.selectItemStatus == true) {
                selectItemEvent.addSelectEvent();
            }
            // document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
        });
    }
}
export const itemPrices = {
    prices: [],
    findItem(itemName) {
        let item = itemName.toLowerCase();
        let array = this.prices.filter((element) => {
            if (element.item === item)
                return element.store_value;
        });
        if (array.length > 0) {
            return array[0].store_value;
        }
        else {
            return "N/A";
        }
    },
    get() {
        let data = "model=Item" + "&method=getPrices";
        ajaxG(data, function (response) {
            itemPrices.prices = response[1].prices;
        });
    }
};
