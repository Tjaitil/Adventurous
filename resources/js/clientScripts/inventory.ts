import { ItemPricesResponse } from './../types/Responses/PricesResponse';
import { ItemSelector } from '../ItemSelector';
import { itemTitle } from '../utilities/itemTitle';
import { inputHandler } from './inputHandler';
import { getClientPageTitle } from '../utilities/getClientPageTitle';
import { AdvApi } from '../AdvApi';
import { CustomFetchApi } from '../CustomFetchApi';
import createHTMLNode from '../utilities/createHTMLNode';
import { useInventoryStore } from '@/ui/stores/InventoryStore';

/**
 * @deprecated
 */
export async function getInventory() {
  AdvApi.get('/inventory').then(data => {
    document.getElementById('inventory').innerHTML = data['html']['inventory'];

    if (getClientPageTitle() == 'Stockpile') {
      const figures = document
        .getElementById('inventory')
        .querySelectorAll('figure');
      figures.forEach(element =>
        { element.addEventListener(
          'click',
          inputHandler.currentBuildingModule.show_menu,
        ); },
      );
      itemTitle.removeTitleEvent();
    } else {
      itemTitle.addTitleEvent();
    }
    itemPrices.get();
    if (ItemSelector.isEventSet) {
      ItemSelector.addSelectEventToInventory();
    }
    // document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
  });
}

/**
 *
 * @deprecated
 */
export async function updateInventory(page = false, addSelect = false) {
  await fetch('handlers/handlerf.php?file=inventory')
    .then(response => response.text())
    .then(data => {
      document.getElementById('inventory').innerHTML = data;

      if (getClientPageTitle() == 'Stockpile') {
        const figures = document
          .getElementById('inventory')
          .querySelectorAll('figure');
        figures.forEach(element =>
          { element.addEventListener(
            'click',
            inputHandler.currentBuildingModule.show_menu,
          ); },
        );
        itemTitle.removeTitleEvent();
      } else {
        itemTitle.addTitleEvent();
      }
      itemPrices.get();
      if (ItemSelector.isEventSet) {
        ItemSelector.addSelectEventToInventory();
      }
    });
}

/**
 *
 * @deprecated
 */
export function checkInventoryStatus() {
  // Fetch items amount
  const items = document.getElementsByClassName('inventory_item');

  const status = items.length === 18;

  const inventoryStatusElement = document.getElementById('inventory-status');
  // Adjust color according to inventory status
  if (status) {
    inventoryStatusElement.classList.add('not-able-color');
  } else {
    inventoryStatusElement.classList.remove('not-able-color');
  }
  return status;
}

/**
 * @deprecated Use inventory store instead
 */
export class Inventory {
  private static itemsElements: Element[];
  private static itemsAmount: number;
  private static isInited: boolean = true;

  static init() {
    if (this.isInited) return;
    this.itemsElements = [...document.querySelectorAll('.inventory_item')];
    this.itemsAmount = this.itemsElements.length;
    this.isInited = true;
  }

  static async update() {
    useInventoryStore().setShouldUpdateInventory(true);
    ItemSelector.isEventSet = false;
  }

  static isFull() {
    return useInventoryStore().isInventoryFull;
  }

  static styleSpaceIndicator(status: 'full' | '') {
    const inventoryStatusElement = document.getElementById('inventory-status');
    // Adjust color according to inventory status
    if (status === 'full') {
      inventoryStatusElement.classList.add('not-able-color');
    } else {
      inventoryStatusElement.classList.remove('not-able-color');
    }
  }

  static get items() {
    return this.itemsElements;
  }
}
interface response {
  html: any;
}

interface ItemPrice {
  name: string;
  store_value: number;
}
export const itemPrices = {
  prices: <ItemPrice[]>[],
  findItem(itemName) {
    const item = itemName.toLowerCase();
    const array = this.prices.filter(element => {
      if (element.name === item) return element.store_value;
    });
    if (array.length > 0) {
      return array[0].store_value;
    } else {
      return 'N/A';
    }
  },
  get() {
    CustomFetchApi.get<ItemPricesResponse>('/inventory/prices')
      .then(response => {
        this.prices = response.data.prices;
      })
      .catch(() => false);
  },
};
