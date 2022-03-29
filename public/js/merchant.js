    var updateStockCountdown = function(pause = false) {
        time = 0;
        if(pause == true) {
            resetStockTimer();
        }
        else if(pause == 'end') {
            clearTimeout(time);
        }
        function updateStock() {
            var data = "model=Merchant" + "&method=getOffers";
            ajaxJS(data, function(response) {
                if(response[0] != false) {
                    if(document.getElementById("trades") != null) {
                        document.getElementById("merchant-offer-list").innerHTML = response[1].html;
                        addMerchantEvents();
                        resetStockTimer();
                    }
                    
                }
            });
        }
        function resetStockTimer() {
            clearTimeout(time);
            time = setTimeout(updateStock, 15000);
            // 1000 milliseconds = 1 second
        }  
    };
    if(document.getElementById("news_content").children[2] != null) {
        addMerchantEvents();
        updateStockCountdown(true);
        getMerchantCountdown();
        if(typeof(document.getElementById("traderAssignment_progressBar")) != undefined) {
            // Calculate progress
            progressBar.calculateProgress(document.getElementById("traderAssignment_progressBar"), false, false, true);    
        }
        document.getElementById("trade_button").addEventListener("click", tradeItem);
    }
    function getMerchantCountdown() {
        let data = "&model=Merchant" + "&method=getMerchantCountdown";
        ajaxG(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                let responseText = response[1];
                let time = (parseInt(responseText.date) + 14400) * 1000;
                let x = setInterval (function() {
                    let now = new Date().getTime();
                    let distance = time - now;
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";   
                    }
                    if(distance < 1) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "0";
                    }
                }, 1000);
            }
        });
    }
    
    function addMerchantEvents() {
        var trades = document.getElementById("trades").querySelectorAll(".merchant-offer");
        if(trades.length > 0) {
            trades.forEach(function(element) {
                // Add eventListener to each node
                element.addEventListener('click', selectTrade);
            });
            return false;
        }
        selectItemEvent.addSelectEvent();
        var button = document.getElementById("do_trade").querySelectorAll("button")[0];
        button.addEventListener("click", tradeItem);
    }
    function selectTrade() {
        // If trades div is hidden return false, because then the tab visible is trader assignment
        if(document.getElementById("trades").visibility == "hidden" || document.getElementById("trades") == null) {
            return false;
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        let item = event.target.closest(".merchant-offer").querySelectorAll("figcaption")[0].innerHTML;
        if(item === "Gold") {
            gameLogger.addMessage("You cannot sell gold!");
            gameLogger.logMessages();
            return false;
        }
        let elementDiv;
        let price;
        if(!event.target.closest(".merchant-offer")) {
            // Item is in inventory
            elementDiv = event.target.closest(".inventory_item");
            // Check if player is in fagna
            if(document.title.indexOf("Fagna") == -1) {
                // Check if the merchant is interested in that item
                let items = document.getElementById("trades_container").querySelectorAll(".tooltip");
                let match = false;
                for(var i = 0; i < items.length; i++) {
                    if(item === items[i].innerHTML) {
                        match = true;
                        price = items[i].closest(".item").querySelectorAll(".item_sell_price")[0].innerHTML.trim();
                        break;
                    }
                }
                if(match === false) {
                    gameLogger.addMessage("This merchant is not interested in that item");
                    gameLogger.logMessages();
                    return false;
                }
            }
        }
        else {
            elementDiv = event.target.closest(".merchant-offer");
            price = elementDiv.querySelectorAll(".item_buy_price")[0].innerHTML.trim();
        }
        let amount = elementDiv.querySelectorAll(".merchant-offer-amount")[0].innerHTML;
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("selected_trade").innerHTML = "";
        document.getElementById("selected_trade").appendChild(figure);
        document.getElementById("do_trade").querySelectorAll("#amount")[0].max = amount;
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
    }
    function tradeItem() {
        if(document.getElementById("selected_trade").children[0] == undefined) {
            gameLogger.addMessage("ERROR: Select a trade!");
            gameLogger.logMessages();
            return false;
        }
        let item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        let amount = document.getElementById("amount").value;
        let mode = document.getElementById("do_trade").querySelectorAll("button")[0].innerText.toLowerCase();
        if(mode !== "buy" && mode !== "sell") {
            gameLogger.addMessage("Trade mode doesn't exists");
            gameLogger.logMessages();
            return false;
        }
        if(amount.length == 0) {
            gameLogger.addMessage("ERROR: Select your amount");
            gameLogger.logMessages();
            return false;
        }
        var data = "model=Merchant" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount + "&mode=" + mode;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                updateDiplomacyTab();
                updateInventory();
                updateStock();
                updateStockCountdown(true);
                document.getElementById("trades_container").innerHTML = responseText.html;
                document.getElementById("selected_trade").innerHTML = "";
                document.getElementById("do_trade").querySelectorAll("p")[1].innerHTML = "";
                document.getElementById("amount").value = "0";
            }
        });
    }