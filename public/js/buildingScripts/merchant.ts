import { ItemPriceResponse } from '../types/responses/MerchantResponses.js';
import { Inventory } from './../clientScripts/inventory.js';
import { selectItemEvent } from '../ItemSelector.js';
import { commonMessages, gameLogger } from './../utilities/gameLogger.js';
import traderModule from './trader.js';
import countdown from '../utilities/countdown.js';
import { AdvApi } from '../AdvApi.js';
import { checkInventoryStatus } from '../clientScripts/inventory.js';
import { advAPIResponse } from '../types/responses/AdvResponse';

const merchantModule = {
    stockTimerId: null,
    init() {

        this.updateStockCountdown(true);
        this.getMerchantCountdown();
        this.addMerchantEvents();
        selectItemEvent.addSelectEvent();
        if (traderModule.init) traderModule.init();
        document.getElementById("trade_button").addEventListener("click", () => this.tradeItem());
    },
    updateStockCountdown(pause = false, end?: boolean) {
        if (end === true) {
            clearTimeout(this.stockTimerId);
        }
        else if (pause == true) {
            this.resetStockTimer();
        }
    },
    updateStock() {
        let data = "model=Merchant" + "&method=getOffers";

        // TODO: Fix api endpoint
        AdvApi.get<advAPIResponse>('/').then((response) => {
            if (response.html['store'] !== undefined) {
                document.getElementById("merchant-offer-list").innerHTML = response.html['store'];
                this.addMerchantEvents();
                this.resetStockTimer();
            }
        })
    },
    resetStockTimer() {
        clearTimeout(this.stockTimerId);
        this.stockTimerId = setTimeout(() => {
            if (this.updateStock) {
                this.updateStock()
            }
        }, 15000);
    },
    getMerchantCountdown() {
        let data = "&model=Merchant" + "&method=getMerchantCountdown";

        // TODO: Fix api endpoint
        AdvApi.get('/').then((response) => {
            let responseText = response[1];
            let endTime = (parseInt(responseText.date) + 14400) * 1000;
            let x = setInterval(() => {
                let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                if (document.getElementById("trades_countdown_time") == null) {
                    clearInterval(x);
                }
                else if (remainder < 1) {
                    document.getElementById("trades_countdown_time").innerHTML = "0";
                    this.updateStock();
                    clearInterval(x);
                }
                else {
                    document.getElementById("trades_countdown_time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
                }
            });
        });
    },
    addMerchantEvents() {
        let trades = document.getElementById("trades").querySelectorAll(".merchant-offer");
        if (trades.length > 0) {
            trades.forEach(element =>
                element.addEventListener('click', event => this.selectTrade(event))
            );
        }
    },
    selectTrade(event) {

        const getPrice = (item: string) => {

            AdvApi.get<ItemPriceResponse>('/').then((response) => {
                document.getElementById("trade_price").querySelectorAll("span")[0].innerText = "" + response.data.price;
            });
        };

        // If trades div is hidden return false, because then the tab visible is trader assignment
        if (document.getElementById("trades").style.visibility == "hidden"
            || document.getElementById("trades") == null) {
            return false;
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        let elementDiv;
        let price;
        let item;
        if (!event.target.closest(".merchant-offer")) {
            item = event.target.closest(".inventory_item").querySelectorAll("figcaption")[0].innerHTML.trim();
            if (item === "Gold") {
                gameLogger.addMessage("You cannot sell gold!", true);
                return false;
            }
            // Item is in inventory
            elementDiv = event.target.closest(".inventory_item");
            // Check if player is in fagna
            if (document.title.indexOf("Fagna") == -1) {
                // Check if the merchant is interested in that item
                let items = document.getElementById("merchant-offer-container").querySelectorAll(".tooltip");
                let match = false;
                for (var i = 0; i < items.length; i++) {
                    if (item === items[i].innerHTML) {
                        match = true;
                        price = items[i].closest(".merchant-offer").querySelectorAll(".item_sell_price")[0].innerHTML.trim();
                        break;
                    }
                }
                if (match === false) {
                    gameLogger.addMessage("This merchant is not interested in that item", true);
                    return false;
                }
            }
        }
        else {
            elementDiv = event.target.closest(".merchant-offer");
            price = elementDiv.querySelectorAll(".item_buy_price")[0].innerHTML.trim();
            item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.trim();
            let amount = elementDiv.querySelectorAll(".merchant-offer-amount")[0].innerHTML;
            let amountElement = <HTMLInputElement>document.getElementById("do_trade").querySelectorAll("#amount")[0]
            amountElement.max = amount;
        }
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("selected_trade").innerHTML = "";
        document.getElementById("selected_trade").appendChild(figure);
        document.getElementById("do_trade").querySelectorAll("p")[0].innerHTML = item;
        document.getElementById("trade_price").querySelectorAll("span")[0].innerText = "" + 0;
        // If location is fagna, fetch price
        if (document.title.indexOf("Fagna") !== -1) {
            getPrice(elementDiv.querySelectorAll(".tooltip")[0].innerHTML);
        }
        else {
            document.getElementById("trade_price").querySelectorAll("span")[0].innerHTML = price;
        }
        let mode;
        if (elementDiv.parentNode.id !== "inventory") {
            mode = "Buy";
        }
        else {
            mode = "Sell";
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].innerText = mode;


    },
    tradeItem() {
        if (checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        if (document.getElementById("selected_trade").children[0] == undefined) {
            gameLogger.addMessage("ERROR: Select a trade!", true);
            return false;
        }
        let item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        let amountElement = <HTMLInputElement>document.getElementById("amount");
        let amount = amountElement.value;
        let mode = document.getElementById("do_trade").querySelectorAll("button")[0].innerText.toLowerCase();
        if (mode !== "buy" && mode !== "sell") {
            gameLogger.addMessage("Trade mode doesn't exists", true);
            return false;
        }
        if (amount.length == 0) {
            gameLogger.addMessage("ERROR: Select your amount", true);
            return false;
        }
        // let data = "model=Merchant" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount + "&mode=" + mode;

        let data = {
            item,
            amount,
            mode,
        };

        // TODO: Fix url
        AdvApi.post('/', data).then((response) => {
            // TODO: Fix this after diplomacy is fixed
            // updateDiplomacyTab();
            Inventory.update();
            this.updateStoreList(response.html['store'] ?? "");

            this.updateStockCountdown(true);
            document.getElementById("selected_trade").innerHTML = "";
            document.getElementById("trade_price").querySelectorAll("span")[0].innerHTML = "";
            amountElement.value = "0";
        })
    },
    updateStoreList(content: string) {
        document.getElementById("merchant-offer-list").innerHTML = content;
        this.addMerchantEvents();
    }
};
export {
    merchantModule as default,
};
