import { AdvApi } from './../AdvApi.js';
import { Inventory } from '../clientScripts/inventory.js';
import storeContainer from '../utilities/storeContainer.js';
import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import { StoreItemResponse } from '../types/responses/StoreItemResponse.js';

const bakeryModule = {
    async init() {

        await this.getData().then(() => {
            storeContainer.init();
            storeContainer.addSelectTrade();
            storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
        });
    },
    make() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};

        let data: BaseBuyStoreItemRequest = {
            item,
            amount
        };

        AdvApi.post('/bakery/make', data).then((response) => {
            Inventory.update();
        })
    },
    async getData() {
        AdvApi.get<StoreItemResponse>('/bakery/store').then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
        });
    },
    onClose() {
        storeContainer.checkItemTooltip();
    },
};
export default bakeryModule;