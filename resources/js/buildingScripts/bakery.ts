import { AdvApi } from './../AdvApi';
import { Inventory } from '../clientScripts/inventory';
import storeContainer from '../utilities/storeContainer';
import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';

const bakeryModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
    });
  },
  make() {
    const { item, amount } = storeContainer.getSelectedTrade() || {};

    const data: BaseBuyStoreItemRequest = {
      item,
      amount,
    };

    AdvApi.post('/bakery/make', data).then(response => {
      Inventory.update();
    });
  },
  async getData() {
    AdvApi.get<StoreItemResponse>('/bakery/store').then(response => {
      storeContainer.setStoreItems(response.data.store_items);
    });
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default bakeryModule;
