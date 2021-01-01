    
import traderModule from './trader.js';
import countdown from '../utilities/countdown.js';

const merchantModule = {
    init() {
        this.updateStockCountdown(true);
        this.getMerchantCountdown();
        this.addMerchantEvents();
        selectItemEvent.addSelectEvent(this.selectTrade);
        if(traderModule.init) traderModule.init();
        document.getElementById("trade_button").addEventListener("click", () => this.tradeItem());
    },
    updateStockCountdown(pause = false) {
        time = 0;
        if(pause == true) {
            this.resetStockTimer();
        }
        else if(pause == 'end') {
            clearTimeout(time);
        }
    },
    updateStock() {
        let data = "model=Merchant" + "&method=getOffers";
        ajaxJS(data, response => {
            if(response[0] != false) {
                if(document.getElementById("trades") != null) {
                    document.getElementById("merchant-offer-list").innerHTML = response[1].html;
                    this.addMerchantEvents();
                    this.resetStockTimer();
                }
                
            }
        });
    },
    resetStockTimer() {
        clearTimeout(time);
        time = setTimeout(() => this.updateStock(), 15000);
    }, 
    getMerchantCountdown() {
        let data = "&model=Merchant" + "&method=getMerchantCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                let endTime = (parseInt(responseText.date) + 14400) * 1000;
                let x = setInterval (function() {
                    let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    } 
                    else if(remainder < 1) {
                        document.getElementById("time").innerHTML = "0";
                        this.updateStock();
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";   
                    }
                }, 1000);
            }
        });
    },
    addMerchantEvents() {
        let trades = document.getElementById("trades").querySelectorAll(".merchant-offer");
        console.log(trades.length);
        if(trades.length > 0) {
            trades.forEach(element => 
                element.addEventListener('click', event => this.selectTrade(event))
            );
            return false;
        }
    },
    selectTrade(event) {
        // If trades div is hidden return false, because then the tab visible is trader assignment
        if(document.getElementById("trades").visibility == "hidden" || document.getElementById("trades") == null) {
            return false;
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        let elementDiv;
        let price;
        let item;
        if(!event.target.closest(".merchant-offer")) {
            item = event.target.closest(".inventory_item").querySelectorAll("figcaption")[0].innerHTML.trim();
            if(item === "Gold") {
                gameLogger.addMessage("You cannot sell gold!", true);
                return false;
            }
            // Item is in inventory
            elementDiv = event.target.closest(".inventory_item");
            // Check if player is in fagna
            if(document.title.indexOf("Fagna") == -1) {
                // Check if the merchant is interested in that item
                let items = document.getElementById("trades_container").querySelectorAll(".tooltip");ftrade
                let match = false;
                for(var i = 0; i < items.length; i++) {
                    if(item === items[i].innerHTML) {
                        match = true;
                        price = items[i].closest(".item").querySelectorAll(".item_sell_price")[0].innerHTML.trim();
                        break;
                    }
                }
                if(match === false) {
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
            document.getElementById("do_trade").querySelectorAll("#amount")[0].max = amount;
        }
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("selected_trade").innerHTML = "";
        document.getElementById("selected_trade").appendChild(figure);
        document.getElementById("do_trade").querySelectorAll("p")[0].innerHTML = item;
        document.getElementById("trade_price").querySelectorAll("span")[0].innerText = 0;
        // If location is fagna, fetch price
        if(document.title.indexOf("Fagna") !== -1) {
            getPrice(elementDiv.querySelectorAll(".tooltip")[0].innerHTML);
        }
        else {
            document.getElementById("trade_price").querySelectorAll("span")[0].innerHTML = price;
        } 
        let mode;
        if(elementDiv.parentNode.id !== "inventory") {
            mode = "Buy";
        }
        else {
            mode = "Sell";
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].innerText = mode;
        function getPrice(item) {
            let data = "model=Merchant" + "&method=getPrice" + "&itemName=" + String(item);
            ajaxG(data, function(response) {
                if(response[0] != false) {
                    let responseText = response[1];
                    document.getElementById("trade_price").querySelectorAll("span")[0].innerText = responseText.price;
                }
            });
            
        }
    },
    tradeItem() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        if(document.getElementById("selected_trade").children[0] == undefined) {
            gameLogger.addMessage("ERROR: Select a trade!", true);
            return false;
        }
        let item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        let amount = document.getElementById("amount").value;
        let mode = document.getElementById("do_trade").querySelectorAll("button")[0].innerText.toLowerCase();
        if(mode !== "buy" && mode !== "sell") {
            gameLogger.addMessage("Trade mode doesn't exists", true);
            return false;
        }
        if(amount.length == 0) {
            gameLogger.addMessage("ERROR: Select your amount", true);
            return false;
        }
        let data = "model=Merchant" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount + "&mode=" + mode;
        ajaxP(data, response => {
            if(response[0] != false) {
                let responseText = response[1];
                updateDiplomacyTab();
                updateInventory();
                this.addMerchantEvents();
                this.updateStockCountdown(true);
                document.getElementById("merchant-offer-list").innerHTML = responseText.html;
                document.getElementById("selected_trade").innerHTML = "";
                document.getElementById("trade_price").querySelectorAll("span")[0].innerHTML = "";
                document.getElementById("amount").value = "0";
            }
        });
    },
    onClose() {

    }
};
export {
    merchantModule as default,
};
