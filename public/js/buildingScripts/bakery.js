import storeContainer from '../utilities/storeContainer.js';

const bakeryModule = {
    init() {
        itemTitle.addItemClassEvents();
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
        [...document.getElementsByClassName("container-item")].forEach(element => 
            element.addEventListener("click", event => this.getIngredients(event)));
        this.getData();
    },
    data: null,
    getIngredients(event) {
        let elementDiv = event.currentTarget.closest(".container-item");
        let item = elementDiv.querySelectorAll("figcaption")[0].innerHTML.toLowerCase().trim();
        let itemData = this.data.find(element => element.item === item);
        if(itemData && itemData.ingredients.length > 0) {
            storeContainer.clearRequirementContainer();
            itemData.ingredients.forEach(element => 
                storeContainer.addRequirement(element.ingredient, element.amount, element.ingredient)
            );
        }
    },
    make() {
        let item = document.getElementById("selected_trade").querySelectorAll("figcaption")[0].innerHTML;
        let amount = document.getElementById("amount").value;

        if(amount == 0) {
            gameLogger.addMessage("Please enter a valid quantity", true);
            return false;
        }
        
        let data = "model=Bakery" + "&method=makeMeal" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, response => {
            if(response[0] != false) {
                updateInventory();
            }
        });
    },
    getData() {
        ajaxJS("model=bakery" + "&method=getData", response => {
            if(response[0] != false) {
                this.data = response[1].data;
            }
        });
    },
    onClose() {
        if(document.getElementById("news_content_main_content").querySelectorAll("item_tooltip").length > 0) {
            itemTitle.resetItemTooltip();
        } 
    },
};
export default bakeryModule;