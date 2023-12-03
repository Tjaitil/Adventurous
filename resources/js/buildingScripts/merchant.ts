import { Inventory } from './../clientScripts/inventory';
import traderModule from './trader';
import { AdvApi } from '../AdvApi';
import storeContainer, { StoreItemResource } from '../utilities/storeContainer';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';
import { advAPIResponse } from '../types/Responses/AdvResponse';

const merchantModule = {
    stockTimerId: null,
    async init() {
        await this.getData().then(() => {
            storeContainer.init();
            storeContainer.setOnlySellStoreItems(false);
            storeContainer.addSelectTrade();
            storeContainer.addSelectTradeToInventory();
            storeContainer.addSelectedItemButtonEvent(this.trade);
        });
        if (traderModule.init) traderModule.init();
    },
    async getData() {
        AdvApi.get<StoreItemResponse>('/merchant/store/items').then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
        });
    },
    updateStockCountdown(pause = false, end?: boolean) {
        if (end === true) {
            clearTimeout(this.stockTimerId);
        }
        else if (pause == true) {
            this.resetStockTimer();
        }
    },
    async updateStock() {
        AdvApi.get<StoreResponse>('/merchant/store').then((response) => {
            storeContainer.setStoreItems(response.data.store_items);
            storeContainer.setNewStoreItemsUI(response.html.storeItemList);
        });
    },
    resetStockTimer() {
        clearTimeout(this.stockTimerId);
        this.stockTimerId = setTimeout(() => {
            if (this.updateStock) {
                this.updateStock()
            }
        }, 15000);
    },
    getMerchantCountdown() {
        let data = "&model=Merchant" + "&method=getMerchantCountdown";

        // TODO: Fix api endpoint
        //     let responseText = response[1];
        //     let endTime = (parseInt(responseText.date) + 14400) * 1000;
        //     let x = setInterval(() => {
        //         let { remainder, hours, minutes, seconds } = countdown.calculate(endTime);
        //         if (document.getElementById("trades_countdown_time") == null) {
        //             clearInterval(x);
        //         }
        //         else if (remainder < 1) {
        //             document.getElementById("trades_countdown_time").innerHTML = "0";
        //             this.updateStock();
        //             clearInterval(x);
        //         }
        //         else {
        //             document.getElementById("trades_countdown_time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
        //         }
        //     });
        // });
    },
    trade() {
        let result = storeContainer.getSelectedTrade();
        if (!result) return;

        if (storeContainer.isTradeNotStoreItem) {
            AdvApi.post('/merchant/trade/open', result).then(async (response) => {
                Inventory.update();
            });
        } else {
            AdvApi.post<StoreResponse>('/merchant/trade', result).then(async (response) => {
                await Inventory.update().then(() => {
                    storeContainer.setStoreItems(response.data.store_items);
                    storeContainer.setNewStoreItemsUI(response.html.storeItemList);
                    storeContainer.resetUI();
                    storeContainer.addSelectTradeToInventory();
                });
                // Update diplomacy tab
            })
        }
    },
};
export {
    merchantModule as default,
};

interface StoreResponse extends advAPIResponse {
    html: {
        storeItemList: string;
    },
    data: {
        store_items: StoreItemResource[];
    }
}
