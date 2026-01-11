import type { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';
import type { StoreItemResponse } from './../types/Responses/StoreItemResponse';
import { AdvApi } from './../AdvApi';
import storeContainer from '../utilities/storeContainer';
import { Inventory } from '../clientScripts/inventory';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { archeryShopDataLoader } from './buildingLoaders';

const archeryShopModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.fletch, 'fletch');
    });
  },
  async getData() {
    const cache = buildingDataPreloader.getBuildingCache('archeryshop');
    if (cache?.store_items) {
      storeContainer.setStoreItems(cache.store_items);
      return;
    }

    await archeryShopDataLoader
      .store_items()
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
      .then(() => Inventory.update())
      .catch(() => false);
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default archeryShopModule;
