import { MakeBakeryItem } from './../types/requests/BakeryRequests';
import { AdvApi } from './../AdvApi';
import { Inventory } from '../clientScripts/inventory.js';
import storeContainer from '../utilities/storeContainer.js';
import { advAPIResponse } from '../types/responses/AdvResponse';

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
        if (itemData && itemData.ingredients.length > 0) {
            storeContainer.clearRequirementContainer();
            itemData.ingredients.forEach(element =>
                storeContainer.addRequirement(element.ingredient, element.amount, element.ingredient)
            );
        }
        document.getElementById("store-container-item-information").innerHTML = "Heal per item " + itemData.heal;
    },
    make() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if (!item) return;
        let data: MakeBakeryItem = {
            item,
            amount
        };
        AdvApi.post('/bakery/make', data).then((response) => {
            Inventory.update();
        })
    },
    getData() {
        // TODO: This is the wrong response type
        AdvApi.get('/bakery/get').then((response) => {
            this.data = response.data;
        });
    },
    onClose() {
        storeContainer.checkItemTooltip();
    },
};
export default bakeryModule;