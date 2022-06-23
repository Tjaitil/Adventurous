const travelBureauModule =  {
    init() {
        [...document.getElementsByClassName("travel_burea_buy_event")].forEach(element =>
            element.addEventListener("click", event => this.buyItem(event))
        );
    },
    buyItem(event) {
        let itemContainer = event.currentTarget.closest(".cart-container-item")
                    .querySelectorAll(".cart-container-item-type")[0];
        if(!itemContainer) return false;
        let item = itemContainer.innerHTML.trim();
        let data = "model=TravelBureau" + "&method=buyItem" + "&item=" + item;
        ajaxP(data, response => {
           if(response[0] != false) {
                updateInventory();
                let responseText = response[1];
                itemContainer.innerHTML = responseText.cart;
           }
        });
    },
    onClose() {
        itemTitle.resetItemTooltip();
    }
}
export default travelBureauModule;