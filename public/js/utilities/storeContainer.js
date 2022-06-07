const storeContainer = {
    addSelectedItemButtonEvent(func, text = false) {
        document.getElementById("container-item-selected-event-button").addEventListener("click", () => func());
        // Custom text for button
        if(text) document.getElementById("container-item-selected-event-button").innerHTML = text;
    },
    addSelectTrade() {
        [...document.getElementsByClassName("container-item")].forEach(element => 
            element.addEventListener("click", event => this.selectTrade(event)));
    },
    selectTrade(event) {
        document.getElementById("do_trade").querySelectorAll("button")[0].disabled = false;
        let elementDiv = event.currentTarget.closest(".container-item");
        let price = elementDiv.querySelectorAll(".container-item-price")[0].innerHTML.trim();
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.trim();
        let figure = elementDiv.querySelectorAll("figure")[0].cloneNode(true);
        document.getElementById("selected_trade").innerHTML = "";
        document.getElementById("selected_trade").appendChild(figure);
        document.getElementById("do_trade").querySelectorAll("p")[0].innerHTML = item;
        document.getElementById("trade_price").querySelectorAll("span")[0].innerText = 0;
        document.getElementById("trade_price").querySelectorAll("span")[0].innerHTML = price;
    },
    addRequirement(name, amount, imgSrc) {
        // Add requirements to storeContainer

        document.getElementById("container-item-requirements").innerHTML = "";
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
        document.getElementById("container-item-requirements").append(div);
    }
}
export default storeContainer;