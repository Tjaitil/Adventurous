import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import { StoreItemResponse } from './../types/responses/StoreItemResponse';
import { AdvApi } from "./../AdvApi.js";
import storeContainer from "../utilities/storeContainer.js";
import { Inventory } from "../clientScripts/inventory.js";

const archeryShopModule = {
    init() {
        this.getData();
        storeContainer.addSelectTrade();
        storeContainer.addSelectedItemButtonEvent(this.fletch, "fletch");
    },
    getData() {
        AdvApi.get<StoreItemResponse>("/archeryshop/get").then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
        }).catch(() => false);
    },
    fletch() {
        let result = storeContainer.getSelectedTrade();
        if (!result) return;

        let { item, amount } = result;

        let data: BaseBuyStoreItemRequest = {
            item,
            amount,
        }

        AdvApi.post('/archeryshop/fletch', data).then((response) =>
            Inventory.update()
        ).catch(() => false);
    },
    onClose() {
        storeContainer.checkItemTooltip();
    },
};
export default archeryShopModule;
