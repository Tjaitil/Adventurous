import storeContainer from '../utilities/storeContainer.js';

const bakeryModule = {
    init() {
        this.getData();
        storeContainer.addSelectTrade();
        storeContainer.addRequirementEvent(event => this.getIngredients(event));
        storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
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
        document.getElementById("store-container-item-information").innerHTML = "Heal per item " + itemData.heal;
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