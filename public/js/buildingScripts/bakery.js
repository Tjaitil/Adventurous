import storeContainer from '../utilities/storeContainer.js';

const bakeryModule = {
    init() {
        itemTitle.addItemClassEvents();
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
        [...document.getElementsByClassName("store-container-item")].forEach(element => 
            element.addEventListener("click", event => this.getIngredients(event)));
        this.getData();
    },
    data: null,
    getIngredients(event) {
        let elementDiv = event.currentTarget.closest(".store-container-item");
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
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if(!item) return;
        
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
        storeContainer.checkItemTooltip();
    },
};
export default bakeryModule;