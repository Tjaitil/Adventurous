function updateInventory(page, addSelect = false) {
    ajaxRequest = new XMLHttpRequest();
    ajaxRequest.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("inventory").innerHTML = this.responseText;
            if (document.getElementsByClassName("page_title").length > 0) {
                if (document.getElementsByClassName("page_title")[0].innerText == "Stockpile") {
                    var buttons = document.getElementById("inventory").querySelectorAll("div");
                    buttons.forEach(function (element) {
                        // ... code code code for this one element
                        element.addEventListener('click', show_menu);
                    });
                    (() =>itemTitle.removeTitleEvent())();
                }
                else {
                    (() =>itemTitle.addTitleEvent())();
                }
                itemPrices.get();
            }
            if(selectItemEvent.selectItemStatus == true) {
                selectItemEvent.addSelectEvent();
            }
            document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
        }
    };
    ajaxRequest.open('GET', "handlers/handlerf.php?file=inventory" + "&page=" + page);
    ajaxRequest.send();
}
const itemPrices = {
    prices: [],
    findItem(itemName) {
        let item = itemName.toLowerCase();

        let array = this.prices.filter((element) => {
            if(element.item === item) return element.store_value;
        });
        if(array.length > 0) {
            return array[0].store_value;
        }
        else {
            return "N/A";
        }
    },
    get() {
        let data = "model=item" + "&method=getPrices";
        ajaxG(data, function(response) {
            itemPrices.prices = response[1].prices;
        });
    }
}