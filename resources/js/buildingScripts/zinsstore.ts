import { Inventory } from './../clientScripts/inventory';
import { AdvApi } from '../AdvApi';
import storeContainer from '../utilities/storeContainer';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';

const zinsStoreModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.trade, 'Sell');
    });
  },
  async getData() {
    AdvApi.get<StoreItemResponse>('/zinsstore/store').then(response => {
      storeContainer.setStoreItems(response.data.store_items);
    });
  },
  trade() {
    const { item, amount } = storeContainer.getSelectedTrade() || {};
    if (!item) return;

    AdvApi.post('/zinsstore/sell', {
      item,
      amount,
    }).then(() => {
      Inventory.update();
    });
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default zinsStoreModule;
