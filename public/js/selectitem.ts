import { gameLogger } from './utilities/gameLogger.js';
import { inputHandler } from './clientScripts/inputHandler.js';
import { itemTitle } from './utilities/itemTitle.js';

/*if(document.getElementById("inventory") != null) {
        addSelectEvent(false);
    }*/

function select(event) {
    let element = event.target.closest("figure");
    console.log(element);
    var figure = element.cloneNode(true);
    /*img.removeAttribute("onclick");*/
    figure.children[0].style.height = "50px";
    figure.children[0].style.width = "50px";
    figure.children[1].style.visibility = "hidden";
    /*figure.className = "item";*/
    var parent = document.getElementById("selected");
    parent.innerHTML = "";
    parent.appendChild(figure);
    let pageTitle = <HTMLElement>document.getElementsByClassName("page_title")[0];
    switch (pageTitle.innerText) {
        case "Armory":
            inputHandler.currentBuildingModule.default.toggleOption();
            break;
        case "Tavern":
            inputHandler.currentBuildingModule.default.getHealingAmount(
                element.querySelectorAll("figcaption")[0].innerHTML
            );
            break;
        default:
            break;
    }
}
// function select_i() {
//     var element = event.target.closest("figure");
//     toggleOfferType();
//     var item = element.children[1].innerHTML.toLowerCase().trim();
//     if (item === "gold") {
//         gameLogger.addMessage("ERROR: You cannot sell gold!");
//         gameLogger.logMessages();
//         return false;
//     }
//     document.getElementById("item_name").value = jsUcWords(item);
//     let img = element.cloneNode(true);
//     img.removeChild(img.children[1]);
//     img.removeAttribute("onclick");
//     let parent = document.getElementById("selected");
//     parent.innerHTML = "";
//     parent.appendChild(img);
// }
export const selectItemEvent = {
    selectItemStatus: false,
    page: "",
    addSelectEvent() {
        this.selectItemStatus = true;
        let figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach((element) => {
            let page_title = <HTMLElement>document.getElementsByClassName("page_title")[0];
            this.page = page_title.innerText;
            if (this.page === "Market") {
                // element.addEventListener("click", select_i);
            } else if (this.page === "Merchant") {
                element.addEventListener("click", (event) =>
                    inputHandler.currentBuildingModule.default.selectTrade(event)
                );
            } else {
                element.addEventListener("click", select);
            }
        });
    },
    removeSelectEvent() {
        this.selectItemStatus = false;

        let inventory = document.getElementById("inventory");
        let newInventory = inventory.cloneNode(true);
        document.getElementById("client-container").replaceChild(newInventory, inventory);
        itemTitle.addTitleEvent();
    },
};
// export const selectItemConv = {
//     eventStatus: false,
//     addEvent() {
//         /*if(document.getElementById("conversation_container").style.visibility !== "visible") {
//                 return false;
//             }*/
//         eventStatus = true;
//         let figures = document.getElementById("inventory").querySelectorAll("figure");
//         figures.forEach(function (element) {
//             element.addEventListener("click", selectItemConv.selectItem);
//         });
//         highlightInventory.set();
//     },
//     removeEvent() {
//         eventStatus = false;
//         let figures = document.getElementById("inventory").querySelectorAll("figure");
//         figures.forEach(function (element) {
//             element.removeEventListener("click", selectItemConv.selectItem);
//         });
//         highlightInventory.clear();
//     },
//     selectItem() {
//         let figure = event.target.closest("figure");
//         let item = figure.children[1].innerHTML.toLowerCase();
//         conversation.getNextLine(item);
//     },
// };
export function selectedCheck(amount_r = true) {
    if (document.getElementById("selected").getElementsByTagName("figure").length == 0) {
        gameLogger.addMessage("Please select a valid item");
        gameLogger.logMessages();
        return false;
    }
    let div = document.getElementById("selected");
    let item = document.getElementById("selected").querySelectorAll("figcaption")[0].innerHTML.toLowerCase().trim();
    // amount_r is variable that opens up for checking only item or item and amount
    if (amount_r === true) {
        let inputElement = <HTMLInputElement>document.getElementById("selected_amount");
        let amount = inputElement.value;
        if (parseInt(amount) === 0) {
            gameLogger.addMessage("Please select a valid amount");
            gameLogger.logMessages();
            return false;
        }
        return [item, amount];
    } else {
        return [item];
    }
}
