    
    window.onload = function () {
        var trades = document.getElementById("trades").querySelectorAll(".store_trade");
        trades.forEach(function(element) {
            // Add eventListener to each node
            element.addEventListener('click', function() {
                selectTrade();
            });
        });
        var button = document.getElementById("do_trade").querySelectorAll("button")[0];
        button.addEventListener("click", buyItem);
        button.disabled = true;
    };
    
    
    function selectTrade() {
        console.log(event.target.tagName);
        if(event.target.tagName == 'IMG') {
            return false;
        }
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        var trade = event.target.closest(".store_trade");
        document.getElementById("selected_trade").innerHTML = trade.innerHTML;
    }
    
    function buyItem() {
        if(document.getElementById("selected_trade").children[0] == undefined) {
            gameLog("ERROR: Select a trade!");
            return false;
        }
        var item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        var amount = document.getElementById("amount").value;
        var bond = document.getElementById("bond").checked;
        if(amount.length == 0) {
            gameLog("ERROR: Select your amount!");
            return false;
        }
        var data = "model=Merchant" + "&method=buyItem" + "&item=" + item + "&amount=" + amount + "&bond=" + bond;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                updateStock();
            }
        });
    }
    
    function updateStock() {
        var data = "model=Merchant" + "&method=getData";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
                document.getElementById("trades").children[0].innerHTML = response[1];
                var trades = document.getElementById("trades").querySelectorAll(".store_trade");
                trades.forEach(function(element) {
                    // Add eventListener to each node
                    element.addEventListener('click', function() {
                        selectTrade();
                    });
                });
            }
        }); 
    }