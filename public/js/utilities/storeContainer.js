const storeContainer = {
    addSelectedItemButtonEvent(func, text = false) {
        document.getElementById("store-container-item-event-button").addEventListener("click", () => func());
        // Custom text for button
        if(text) document.getElementById("store-container-item-event-button").innerHTML = text;
    },
    addSelectTrade() {
        [...document.getElementsByClassName("store-container-item")].forEach(element => 
            element.addEventListener("click", event => this.selectTrade(event)));
    },
    selectTrade(event) {
        document.getElementById("store-container-do-trade").querySelectorAll("button")[0].disabled = false;
        let elementDiv = event.currentTarget.closest(".store-container-item");
        let price = elementDiv.querySelectorAll(".store-container-item-price")[0].innerHTML.trim();
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.trim();
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("store-container-selected-trade").innerHTML = "";
        document.getElementById("store-container-selected-trade").appendChild(figure);
        document.getElementById("store-container-do-trade").querySelectorAll("p")[0].innerHTML = item;
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerText = 0;
        document.getElementById("store-contaniner-trade-price").querySelectorAll("span")[0].innerHTML = price;
    },
    getSelectedTrade() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }

        let item = document.getElementById("store-container-selected-trade")
                   .querySelectorAll("figcaption")[0]
                   .innerHTML
                   .toLowerCase()
                   .trim();
        let amount = document.getElementById("store-container-selected-trade-amount").value;

        if(amount == 0) {
            gameLogger.addMessage("Please enter a valid quantity", true);
            return false;
        } else if(!item) {
            gameLogger.addMessage("Please select an item", true);
        }

        return {
            item,
            amount
        }
    },
    clearRequirementContainer() {
        document.getElementById("store-container-item-requirements").innerHTML = "";
    },
    addRequirement(name, amount, imgSrc) {
        // Add requirements to storeContainer
        // Create div
        let div = document.createElement("div");
        div.classList.add("item");
        let figure = document.createElement("figure");
        figure.appendChild(document.createElement("img"));
        figure.appendChild(document.createElement("figcaption"));
        div.appendChild(figure);
        let span = document.createElement("spam");
        span.classList.add("item_amount");
        div.appendChild(span);
        div.querySelectorAll("figcaption")[0].innerHTML = name;
        div.querySelectorAll("img")[0].src = "public/images/" + imgSrc + ".png";
        div.querySelectorAll(".item_amount")[0].innerHTML = amount;

        // ADd itemtitle events
        div.addEventListener("mouseenter", () => itemTitle.show());
        div.addEventListener("mouseleave", () => itemTitle.hide());
        document.getElementById("store-container-item-requirements").append(div);
    },
    checkItemTooltip() {
        if(document.getElementById("news_content_main_content").querySelectorAll("item_tooltip").length > 0) {
            itemTitle.resetItemTooltip();
        }
    }
}
export default storeContainer;