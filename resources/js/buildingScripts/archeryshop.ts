import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import { StoreItemResponse } from './../types/Responses/StoreItemResponse';
import { AdvApi } from './../AdvApi';
import storeContainer from '../utilities/storeContainer';
import { Inventory } from '../clientScripts/inventory';

const archeryShopModule = {
    init() {
        this.getData();
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.fletch, 'fletch');
    },
    getData() {
        AdvApi.get<StoreItemResponse>('/archeryshop/get')
            .then(response => {
                storeContainer.setStoreItems(response.data.store_items);
            })
            .catch(() => false);
    },
    fletch() {
        const result = storeContainer.getSelectedTrade();
        if (!result) return;

        const { item, amount } = result;

        const data: BaseBuyStoreItemRequest = {
            item,
            amount,
        };

        AdvApi.post('/archeryshop/fletch', data)
            .then(response => Inventory.update())
            .catch(() => false);
    },
    onClose() {
        storeContainer.checkItemTooltip();
    },
};
export default archeryShopModule;
