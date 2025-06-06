import { AdvApi } from './../AdvApi';
import { ClientOverlayInterface } from '../clientScripts/clientOverlayInterface';
import { itemTitle } from '../utilities/itemTitle';
import { useInventoryStore } from '@/ui/stores/InventoryStore';

const stockpileModule = {
  toggled: false,
  addEvent() {
    this.toggled = true;
    useInventoryStore().setInventoryItemEvent('stockpileMenu');
  },
  removeEvent() {
    this.toggled = false;

    useInventoryStore().setInventoryItemEvent(null);
  },
  menuListItemInputIndex: 2,
  init() {
    itemTitle.hideItemTooltip();
    this.addEvent();
    this.addShowMenuEvent();
    this.addStockpileActions();
  },
  addShowMenuEvent() {
    const figures = document
      .getElementById('stockpile')
      .querySelectorAll('figure');
    figures.forEach(element => {
      element.addEventListener('click', e => {
        this.show_menu(e);
      });
    });
  },
  addStockpileActions() {
    const listElements = document
      .getElementById('stck_menu')
      .querySelectorAll('LI');
    listElements.forEach((element, index) => {
      // First element is the item name, third is the input
      if ([this.menuListItemInputIndex].includes(index)) {
        return;
      }
      element.addEventListener('click', event =>
        stockpileModule.stockpileAction(false, event),
      );
    });
    document
      .getElementById('stck_menu_custom_amount')
      .addEventListener('keyup', event => {
        event.preventDefault();
        if (event.key === 'Enter') {
          stockpileModule.stockpileAction(true, event);
        }
      });
  },
  async stockpileAction(amountSet = false, event: Event) {
    const listItem = event.target as HTMLElement;
    const eventTarget = event.target as HTMLElement;
    const itemName = document.getElementById('stck-current-item').innerHTML;
    let item = itemName.toLowerCase().trim();
    let amount;
    let insert;
    let method;

    if (document.getElementById('stck_menu').closest('#inventory')) {
      method = 'insert';
      insert = true;
    } else {
      method = 'withdraw';
      insert = false;
    }

    const stckMenuInput = <HTMLInputElement>(
      document.getElementById('stck_menu_custom_amount')
    );

    if (amountSet) {
      amount = stckMenuInput.value;
    } else if (eventTarget.id === 'stck_menu_all') {
      let array: HTMLElement[] = [];
      if (insert === true) {
        array = [
          ...document
            .getElementById('inventory')
            .querySelectorAll('.inventory_item'),
        ] as HTMLElement[];
      } else {
        array = [
          ...document
            .getElementById('stockpile')
            .querySelectorAll('.stockpile_item'),
        ] as HTMLElement[];
      }
      const itemElement = array.find(
        element =>
          element.querySelectorAll('figcaption')[0].innerHTML === itemName,
      );
      amount = parseInt(
        itemElement.querySelectorAll('.item_amount')[0].innerHTML,
      );
    } else {
      amount = parseInt(listItem.dataset.actionAmount);
    }

    this.hideMenu();

    item = item.split('<br>')[0];

    const data = {
      insert,
      amount,
      item,
    };

    await AdvApi.post<UpdateStockpileRequest>('/stockpile/update', data)
      .then(async res => {
        document.getElementById('stockpile-list').innerHTML =
          res.html.stockpile;
        document.getElementById('stck_menu')?.classList.add('hidden');

        stckMenuInput.value = '';

        useInventoryStore().setShouldUpdateInventory(true);
        this.addShowMenuEvent();

        ClientOverlayInterface.adjustWrapperHeight();
      })
      .catch(error => {
        return;
      });
  },
  show_menu(event: Event) {
    const element = (event.target as HTMLElement).closest('div');
    const menu = document.getElementById('stck_menu');
    const list = document.getElementById('stck-menu-option-list');
    let item;
    if (element.className == 'inventory_item') {
      item = element?.querySelectorAll('figcaption .tooltip_item')[0].innerHTML;
      document.getElementById('inventory').appendChild(menu);
    } else {
      document.getElementById('news_content').appendChild(menu);
      item = element?.querySelectorAll('figcaption')[0].innerHTML;
    }

    // Insert item name at the first li
    document.getElementById('stck-current-item').innerHTML = item;
    menu?.classList.remove('hidden');
    // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
    let menuTop;
    const listItems = list.querySelectorAll('li');
    let elementPos;
    const inputElement = document.getElementById(
      'stck_menu_custom_amount',
    ) as HTMLInputElement;

    const stockpileMenuItemsLabel = {
      insert: ['Insert 1', 'Insert 5', 'Insert x', 'Insert all'],
      withdraw: ['Withdraw 1', 'Withdraw 5', 'Withdraw x', 'Withdraw all'],
    };

    let currentStockpileMenuItemLabels;

    if (element.className == 'inventory_item') {
      currentStockpileMenuItemLabels = stockpileMenuItemsLabel.insert;
      elementPos = element.getBoundingClientRect();
      if (
        element.offsetTop + 150 >
        document.getElementById('stockpile').offsetHeight
      ) {
        menuTop = element.offsetTop - 70;
      } else {
        menuTop = element.offsetTop - 25;
      }
    } else {
      currentStockpileMenuItemLabels = stockpileMenuItemsLabel.withdraw;
      elementPos = element.getBoundingClientRect();
      if (
        element.offsetTop + 150 >
        document.getElementById('stockpile').offsetHeight
      ) {
        menuTop = element.offsetTop - 40;
      } else {
        menuTop = element.offsetTop - 25;
      }
    }

    for (let i = 0; i < listItems.length; i++) {
      if (i === this.menuListItemInputIndex) {
        inputElement.placeholder = currentStockpileMenuItemLabels[i];
      } else {
        listItems[i].innerHTML = currentStockpileMenuItemLabels[i];
      }
    }

    menu.style.left = element.offsetLeft + 'px';
    menu.style.top = menuTop + 'px';
  },
  hideMenu() {
    const menu = document.getElementById('stck_menu');
    menu?.classList.add('hidden');
    document.getElementById('news_content').appendChild(menu);
  },
  onClose() {
    this.removeEvent();
    const menu = document
      .getElementById('stck_menu')
      ?.parentElement.removeChild(document.getElementById('stck_menu'));
  },
};
export { stockpileModule as default };

export interface UpdateStockpileRequest {
  html: {
    stockpile: string;
  };
}
