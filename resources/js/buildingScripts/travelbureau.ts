import { AdvApi } from '../AdvApi';
import { Inventory } from '../clientScripts/inventory';
import { advAPIResponse } from '../types/Responses/AdvResponse';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';
import { ItemElement } from '../utilities/ItemElement';
import storeContainer from '../utilities/storeContainer';

const travelBureauModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.buyItem, 'Buy');
    });
  },
  async getData() {
    AdvApi.get<StoreItemResponse>('/travelbureau/store')
      .then(response => {
        storeContainer.setStoreItems(response.data.store_items);
      })
      .then(() => false);
  },
  buyItem() {
    const { item, amount } = storeContainer.getSelectedTrade() || {};

    const data: BuyCartRequest = {
      item,
    };

    const currentCartItem = new ItemElement(
      document.getElementById('current-cart'),
      null,
      { showTooltip: false },
    );

    AdvApi.post<BuyCartResponse>('/travelbureau/buy', data)
      .then(response => {
        Inventory.update();
        currentCartItem.replaceItem(response.data.new_cart, 1);
      })
      .then(() => false);
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default travelBureauModule;

interface BuyCartRequest {
  item: string;
}

interface BuyCartResponse extends advAPIResponse {
  data: {
    new_cart: string;
  };
}
