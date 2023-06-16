import { Inventory } from './../clientScripts/inventory';
import { AdvApi } from "../AdvApi.js";
import storeContainer from "../utilities/storeContainer.js";

const zinsStoreModule = {
    init() {
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.trade, 'Sell');
    },

    trade() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};
        if (!item) return;

        AdvApi.post('/zinsstore', {
            item,
            amount
        }).then(() => {
            Inventory.update();
        })
    },
}
export default zinsStoreModule;