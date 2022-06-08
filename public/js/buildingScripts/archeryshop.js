const archeryShopModule = {
    init() {
        [...document.getElementsByClassName("archery-shop-fletch-button")].forEach(element => 
            element.addEventListener('click', this.fletch)
        );
    },
    fletch(event) {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let amount = event.target.parentElement.children[0].value;
        let item = event.target.closest("tr").querySelectorAll("figcaption")[0].innerHTML.toLowerCase();
        event.target.parentElement.children[0].value = "";
        let data = "model=ArcheryShop" + "&method=fletch" + "&item=" + item  + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('ArhceryShop');
            }       
        });
    }
};
export default archeryShopModule;