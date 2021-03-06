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
                        document.getElementById("trades_container").innerHTML = response[1];
                        console.log(document.getElementById("trades_container"));
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
    }
    function getMerchantCountdown() {
        var data = "&model=Merchant" + "&method=getMerchantCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = (parseInt(data[0]) + 14400) * 1000;
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";   
                    }
                    if(distance < 1) {
                        clearInterval(x);
                    }
                }, 1000);
            }
        });
    }
    
    function addMerchantEvents() {
        var trades = document.getElementById("trades").querySelectorAll(".item");
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
        let item = event.target.closest("figure").children[1].innerHTML;
        if(item === "Gold") {
            gameLog("You cannot sell gold!");
            return false;
        }
        let elementDiv;
        let price;
        if(event.target.closest(".item") === null) {
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
                    gameLog("This merchant is not interested in that item");
                    return false;
                }
                else {
                    price = document.createTextNode(price);
                }
            }
        }
        else {
            elementDiv = event.target.closest(".item");
            price = elementDiv.querySelectorAll(".item_price")[0].innerHTML.split("<")[0].split("/")[0].trim();
            price = document.createTextNode(price);
        }
        let amount = elementDiv.querySelectorAll(".item_amount")[0].innerHTML;
        let element = event.target.closest("figure");
        var figure = element.cloneNode(true);
        figure.children[0].style.height = "50px";
        figure.children[0].style.width = "50px";
        document.getElementById("selected_trade").innerHTML = "";
        document.getElementById("selected_trade").appendChild(figure);
        document.getElementById("do_trade").querySelectorAll("#amount")[0].max = amount;
        document.getElementById("do_trade").querySelectorAll("p")[0].innerHTML = item;
        document.getElementById("trade_price").querySelectorAll("span")[0].innerText = 0;
        // If location is fagna, fetch price
        if(document.title.indexOf("Fagna") != -1) {
            getPrice(String(elementDiv.querySelectorAll(".tooltip")[0].innerHTML));
        }
        else {
            document.getElementById("trade_price").querySelectorAll("span")[0].innerText = price;
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
            let data = "model=Merchant" + "&method=getPrice" + "&itemName=" + item;
            ajaxG(data, function(response) {
                if(response[0] != false) {
                    document.getElementById("trade_price").querySelectorAll("span")[0].innerText = response[1];
                }
            });
            
        }
    }
    function tradeItem() {
        if(document.getElementById("selected_trade").children[0] == undefined) {
            gameLog("ERROR: Select a trade!");
            return false;
        }
        let item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        let amount = document.getElementById("amount").value;
        let mode = document.getElementById("do_trade").querySelectorAll("button")[0].innerText.toLowerCase();
        if(mode !== "buy" && mode !== "sell") {
            gameLog("Something unexpected happened");
            return false;
        }
        if(amount.length == 0) {
            gameLog("ERROR: Select your amount!");
            return false;
        }
        var data = "model=Merchant" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount + "&mode=" + mode;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                document.getElementById("trades_container").innerHTML = response[1];
                document.getElementById("selected_trade").innerHTML = "";
                document.getElementById("do_trade").querySelectorAll("p")[1].innerHTML = "";
                document.getElementById("trade_price").querySelectorAll("span")[0].innerText = "";
                document.getElementById("amount").value = "0";
                updateInventory();
                addMerchantEvents();
                updateStockCountdown(true);
            }
            else {
                gameLog(response[1]);
            }
        });
    }