import { Inventory } from './../clientScripts/inventory';
import { AdvApi } from '../AdvApi';
import storeContainer from '../utilities/storeContainer';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { zinsStoreDataLoader } from './buildingLoaders';

const zinsStoreModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.trade, 'Sell');
    });
  },
  async getData() {
    const cache = buildingDataPreloader.getBuildingCache('zinsstore');
    if (cache?.store_items) {
      storeContainer.setStoreItems(cache.store_items);
      return;
    }

    await zinsStoreDataLoader
      .store_items()
      .then(response => {
        storeContainer.setStoreItems(response.data.store_items);
      })
      .catch(() => false);
  },
  async trade() {
    const { item, amount } = storeContainer.getSelectedTrade() || {};
    if (!item) return;

    await AdvApi.post('/zinsstore/sell', {
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
