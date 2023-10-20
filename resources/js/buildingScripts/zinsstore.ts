import { Inventory } from './../clientScripts/inventory.js';
import { AdvApi } from "../AdvApi.js";
import storeContainer from "../utilities/storeContainer.js";
import { StoreItemResponse } from '../types/responses/StoreItemResponse.js';

const zinsStoreModule = {
    async init() {
        await this.getData().then(() => {
            storeContainer.init();
            storeContainer.addSelectTrade();
            storeContainer.addSelectedItemButtonEvent(this.trade, 'Sell');
        });
    },
    async getData() {
        AdvApi.get<StoreItemResponse>('/zinsstore/store').then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
        });
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
    onClose() {
        storeContainer.checkItemTooltip();
    },
}
export default zinsStoreModule;