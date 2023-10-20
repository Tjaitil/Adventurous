import { AdvApi } from "../AdvApi.js";
import { Inventory } from "../clientScripts/inventory.js";
import { advAPIResponse } from "../types/responses/AdvResponse.js";
import { StoreItemResponse } from "../types/responses/StoreItemResponse.js";
import { ItemElement } from "../utilities/ItemElement.js";
import storeContainer from "../utilities/storeContainer.js";

const travelBureauModule = {
    async init() {
        await this.getData().then(() => {
            storeContainer.init();
            storeContainer.addSelectTrade();
            storeContainer.addSelectedItemButtonEvent(this.buyItem, 'Buy');
        });
    },
    async getData() {
        AdvApi.get<StoreItemResponse>('/travelbureau/store').then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
        }).then(() => false);
    },
    buyItem() {
        let { item, amount } = storeContainer.getSelectedTrade() || {};

        let data: BuyCartRequest = {
            item
        }

        let currentCartItem = new ItemElement(document.getElementById("current-cart"), null, { showTooltip: false });

        AdvApi.post<BuyCartResponse>('/travelbureau/buy', data).then((response) => {
            Inventory.update();
            currentCartItem.replaceItem(response.data.new_cart, 1);
        }).then(() => false);
    },
    onClose() {
        storeContainer.checkItemTooltip();
    }
}
export default travelBureauModule;

interface BuyCartRequest {
    item: string;
}

interface BuyCartResponse extends advAPIResponse {
    data: {
        new_cart: string;
    }
}