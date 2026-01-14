import { AdvApi } from './../AdvApi';
import { Inventory } from '../clientScripts/inventory';
import storeContainer from '../utilities/storeContainer';
import type { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import type { StoreItemResponse } from '../types/Responses/StoreItemResponse';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { bakeryDataLoader } from './buildingLoaders';

const bakeryModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.make, 'Make');
    });
  },
  make() {
    const result = storeContainer.getSelectedTrade();
    if (!result) return;

    const { item, amount } = result;

    const data: BaseBuyStoreItemRequest = {
      item,
      amount,
    };

    AdvApi.post('/bakery/make', data)
      .then(() => Inventory.update())
      .catch(() => false);
  },
  async getData() {
    const cache = buildingDataPreloader.getBuildingCache('bakery');
    if (cache?.store_items) {
      storeContainer.setStoreItems(cache.store_items);
      return;
    }

    await bakeryDataLoader
      .store_items()
      .then(response => {
        storeContainer.setStoreItems(response.data.store_items);
      })
      .catch(() => false);
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default bakeryModule;
