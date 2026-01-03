import { GameLogger } from './utilities/GameLogger';
import { inputHandler } from './clientScripts/inputHandler';
import { itemTitle } from './utilities/itemTitle';
import { useInventoryStore } from './ui/stores/InventoryStore';

/**
 *
 * @depcrated
 */
function select(event) {
  const element = event.target.closest('figure');
  const figure = element.cloneNode(true);

  figure.children[0].style.height = '50px';
  figure.children[0].style.width = '50px';
  figure.children[1].style.visibility = 'hidden';

  const item = element
    .querySelectorAll('figcaption .tooltip_item')[0]
    .innerHTML.toLowerCase()
    .trim();

  useInventoryStore().setSelectedItem(item);
  const parent = document.getElementById('selected');
  if (parent === null) {
    return;
  }
  parent.innerHTML = '';
  parent.appendChild(figure);
  const pageTitle = <HTMLElement>(
    document.getElementsByClassName('page_title')[0]
  );
  switch (pageTitle.innerText) {
    case 'Tavern':
      inputHandler.currentBuildingModule.default.getHealingAmount(
        element.querySelectorAll('figcaption .tooltip_item')[0].innerHTML,
      );
      break;
    default:
      break;
  }
}
// function select_i() {
//     var element = event.target.closest("figure");
//     toggleOfferType();
//     var item = element.children[1].innerHTML.toLowerCase().trim();
//     if (item === "gold") {
//         gameLogger.addMessage("ERROR: You cannot sell gold!");
//         gameLogger.logMessages();
//         return false;
//     }
//     document.getElementById("item_name").value = jsUcWords(item);
//     let img = element.cloneNode(true);
//     img.removeChild(img.children[1]);
//     img.removeAttribute("onclick");
//     let parent = document.getElementById("selected");
//     parent.innerHTML = "";
//     parent.appendChild(img);
// }

export class ItemSelector {
  private static eventStatus: boolean = false;
  private static page: string = '';
  private static container: HTMLElement;
  private static selectedWrapper: HTMLElement;
  private static selectedItemAmountInput: HTMLInputElement;
  private static isSelectedAmountInputVisible: boolean = true;

  public static setup() {
    this.container = document.getElementById('selected-item-container');
    this.selectedWrapper = document.getElementById('selected');
    this.selectedItemAmountInput = document.getElementById(
      'selected-item-amount',
    ) as HTMLInputElement;
    this.addSelectEventToInventory();
  }

  public static get isEventSet() {
    return this.eventStatus;
  }

  public static set isEventSet(value: boolean) {
    this.eventStatus = value;
  }

  public static addSelectEventToInventory() {
    this.eventStatus = true;
    useInventoryStore().setInventoryItemEvent('selectItem');
  }

  public static removeSelectEventFromInventory() {
    this.eventStatus = false;

    const inventory = document.getElementById('inventory');
    const newInventory = inventory.cloneNode(true);
    document
      .getElementById('client-container')
      .replaceChild(newInventory, inventory);
    itemTitle.addTitleEvent();
  }

  public static selectItem(event) {
    const element = event.target.closest('figure');
    const figure = element.cloneNode(true);

    figure.children[0].style.height = '50px';
    figure.children[0].style.width = '50px';
    figure.children[1].style.visibility = 'hidden';

    this.selectedWrapper.innerHTML = '';
    this.selectedWrapper.appendChild(figure);
    const pageTitle = <HTMLElement>(
      document.getElementsByClassName('page_title')[0]
    );
    switch (pageTitle.innerText) {
      case 'Armory':
        inputHandler.currentBuildingModule.default.toggleOption();
        break;
      case 'Tavern':
        inputHandler.currentBuildingModule.default.getHealingAmount(
          element.querySelectorAll('figcaption .tooltip_item')[0].innerHTML,
        );
        break;
      default:
        break;
    }
  }

  public static isItemValid(): boolean {
    if (
      document.getElementById('selected').getElementsByTagName('figure')
        .length == 0
    ) {
      GameLogger.addMessage('Please select a valid item');
      GameLogger.logMessages();
      return false;
    }
    if (this.isSelectedAmountInputVisible) {
      const amount = parseInt(this.selectedItemAmountInput.value);

      if (amount <= 0) {
        GameLogger.addMessage('Please enter a valid amount');
        GameLogger.logMessages();
        return false;
      }
    }
  }

  public static get selected(): { name: string; amount: number } {
    if (this.selectedWrapper.getElementsByTagName('figure').length === 0) {
      GameLogger.addMessage('Please select a valid item', true);
    }
    const name = this.selectedWrapper
      .querySelectorAll('figcaption .tooltip_item')[0]
      .innerHTML.toLowerCase()
      .trim();
    // Is input visible?
    if (this.isSelectedAmountInputVisible) {
      const amount = parseInt(this.selectedItemAmountInput.value);
      return { name, amount };
    } else {
      return { name, amount: 1 };
    }
  }

  public static hideSelectedAmountInput() {
    this.isSelectedAmountInputVisible = false;
    this.selectedItemAmountInput.style.display = 'none';
  }

  public static showSelectedAmountInput() {
    this.isSelectedAmountInputVisible = true;
    this.selectedItemAmountInput.style.display = 'block';
  }

  public static clearContainer() {
    this.selectedWrapper.innerHTML = '';
    if (this.isSelectedAmountInputVisible) {
      this.selectedItemAmountInput.value = '0';
    }
  }
}

export const selectItemEvent = {
  selectItemStatus: false,
  page: '',
  addSelectEvent() {
    this.selectItemStatus = true;
    const figures = document
      .getElementById('inventory')
      .querySelectorAll('figure');
    figures.forEach(element => {
      const page_title = <HTMLElement>(
        document.getElementsByClassName('page_title')[0]
      );
      this.page = page_title.innerText;
      if (this.page === 'Market') {
        // element.addEventListener("click", select_i);
      } else if (this.page === 'Merchant') {
        element.addEventListener('click', event =>
          inputHandler.currentBuildingModule.default.selectTrade(event),
        );
      } else {
        element.addEventListener('click', select);
      }
    });
  },
  removeSelectEvent() {
    this.selectItemStatus = false;

    const inventory = document.getElementById('inventory');
    const newInventory = inventory.cloneNode(true);
    document
      .getElementById('client-container')
      .replaceChild(newInventory, inventory);
    itemTitle.addTitleEvent();
  },
};
// export const selectItemConv = {
//     eventStatus: false,
//     addEvent() {
//         /*if(document.getElementById("conversation_container").style.visibility !== "visible") {
//                 return false;
//             }*/
//         eventStatus = true;
//         let figures = document.getElementById("inventory").querySelectorAll("figure");
//         figures.forEach(function (element) {
//             element.addEventListener("click", selectItemConv.selectItem);
//         });
//         highlightInventory.set();
//     },
//     removeEvent() {
//         eventStatus = false;
//         let figures = document.getElementById("inventory").querySelectorAll("figure");
//         figures.forEach(function (element) {
//             element.removeEventListener("click", selectItemConv.selectItem);
//         });
//         highlightInventory.clear();
//     },
//     selectItem() {
//         let figure = event.target.closest("figure");
//         let item = figure.children[1].innerHTML.toLowerCase();
//         conversation.getNextLine(item);
//     },
// };

/**
 *
 * @depcrated
 */
export function selectedCheck(amount_r = true) {
  if (
    document.getElementById('selected').getElementsByTagName('figure').length ==
    0
  ) {
    GameLogger.addMessage('Please select a valid item');
    GameLogger.logMessages();
    return false;
  }
  const div = document.getElementById('selected');
  const item = document
    .getElementById('selected')
    .querySelectorAll('figcaption .tooltip_item')[0]
    .innerHTML.toLowerCase()
    .trim();
  // amount_r is variable that opens up for checking only item or item and amount
  if (amount_r) {
    const inputElement = <HTMLInputElement>(
      document.getElementById('selected_amount')
    );
    const amount = parseInt(inputElement.value);
    if (amount === 0) {
      GameLogger.addMessage('Please select a valid amount');
      GameLogger.logMessages();
      return false;
    }
    return { item, amount };
  } else {
    return { item, amount: 1 };
  }
}
