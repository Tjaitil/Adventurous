import { ClientOverlayInterface } from './../clientScripts/clientOverlayInterface';
import { AdvApi } from './../AdvApi';
import storeContainer from '../utilities/storeContainer';
import { Inventory } from '../clientScripts/inventory';
import { StoreItemResponse } from '../types/Responses/StoreItemResponse';
import { BaseBuyStoreItemRequest } from '../types/requests/BaseBuyStoreItemRequest';

const smithyModule = {
  async init() {
    await this.getData().then(() => {
      storeContainer.init();
      storeContainer.addSelectTrade();
      storeContainer.addSelectedItemButtonEvent(this.smith, 'Smith');
    });
  },
  data: null,
  async getData() {
    AdvApi.get<StoreItemResponse>('/smithy/store')
      .then(response => storeContainer.setStoreItems(response.data.store_items))
      .catch(() => false);
  },
  smith() {
    const result = storeContainer.getSelectedTrade();
    if (!result) return;

    const { item, amount } = result;

    const data: BaseBuyStoreItemRequest = {
      item,
      amount,
    };

    AdvApi.post('/smithy/smith', data)
      .then(() => Inventory.update())
      .catch(() => false);
  },
  showMineral(mineral) {
    if (!mineral) return;
    const arr = <HTMLElement[]>[...document.getElementsByClassName('minerals')];

    arr.forEach(element => {
      if (element.getAttribute('title') == mineral) {
        document.getElementById(element.getAttribute('title')).style.display =
          'inline-block';
        element.classList.add('container_selected_item');
      } else {
        if (!document.getElementById(element.getAttribute('title')))
          return false;
        document.getElementById(element.getAttribute('title')).style.display =
          'none';
        element.classList.remove('container_selected_item');
      }
    });

    ClientOverlayInterface.adjustWrapperHeight();
  },
  onClose() {
    storeContainer.checkItemTooltip();
  },
};
export default smithyModule;
