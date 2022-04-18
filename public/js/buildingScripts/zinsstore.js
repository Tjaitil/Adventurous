    // Check if news_content_main_content -> children[2] has gotten content from game.js -> game.fetchBuilding()
    if(document.getElementById("news_content").children[2] != null) {
        document.getElementById("zinsstore_trade").addEventListener("click", () => {zinsTrade()});
        let items = document.getElementById("news_content_main_content").querySelectorAll(".item");
        items.forEach((element) => {
            element.addEventListener("click", () => selectItem());
        });
    }
    function zinsTrade() {
        let items = document.getElementById("news_content_main_content").querySelectorAll(".item");
        items = [...items];
        let match = items.filter((element) => {
                return (element.querySelectorAll("img")[0].classList.contains("item_selected"));
            }
        );
        let amount = document.getElementById("zinsstore_item_amount").value;
        if(match.length === 0) {
            gameLogger.addMessage("Please select a item to trade");
            gameLogger.logMessages();
            return false;
        }
        if(!amount > 0) {
            gameLogger.addMessage("Please select an amount");
            gameLogger.logMessages();
            return false;
        }
        let item = match[0].querySelectorAll("figcaption")[0].innerHTML.trim().toLowerCase();
        ajaxP("model=ZinsStore" + "&method=tradeItem" + "&item=" + item + "&amount=" + amount, function(response) {
            console.log(response);
            if(response[0] !== false) {
                updateInventory();
                document.getElementById("zinsstore_item_amount").value = 0;
                items.forEach(element => {
                    element.style.border = "none";
                });
            }
        });
    }
    function selectItem() {
        let element = event.target.closest("div");
        console.log(event.target);
        let items = document.getElementById("news_content_main_content").querySelectorAll(".item");
        console.log(items);
        console.log(element);
        for(let i = 0; i < items.length; i++) {
            if(element.querySelectorAll("figcaption")[0].innerHTML.trim() === 
                items[i].querySelectorAll("figcaption")[0].innerHTML.trim()) {
                items[i].querySelectorAll("img")[0].setAttribute("class", "item_selected");
            }
            else {
                items[i].querySelectorAll("img")[0].classList.remove('item_selected');
            }
        }
    }