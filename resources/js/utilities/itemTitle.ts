import { Game } from '../advclient';
import { itemPrices } from '../clientScripts/inventory';

// Container for item title for items
export const itemTitle = {
  status: true,
  currentTitle: null,
  currentPrice: null,
  computerDevice: true,
  tooltipElement: null,
  subtractLeft: 0,
  currentEvent: null,
  adjustForContainerMismatch() {
    const parentWidth = this.tooltipElement.parentElement.clientWidth;
    const screenwidth = window.innerWidth;
    this.subtractLeft = parentWidth - screenwidth;
  },

  init(computerDevice) {
    this.tooltipElement = document.getElementById('item_tooltip');
    itemTitle.computerDevice = computerDevice;
    this.adjustForContainerMismatch();
    this.addTitleEvent();
  },
  addTitleEvent() {
    this.status = true;
  },
  removeTitleEvent() {
    const elements = document
      .getElementById('inventory')
      .querySelectorAll('figure');
    elements.forEach(element => {
      const clone = element.cloneNode(true);
      element.replaceWith(clone);
    });
    this.status = false;
  },
  addItemClassEvents() {
    // Add events on specific pages
    if (['merchant', 'zinsstore'].indexOf(Game.properties.building) !== -1)
      return false;
    document.getElementById('news_content_main_content');
    const itemDivs = document
      .getElementById('news_content_main_content')
      .querySelectorAll('.item');
    itemDivs.forEach(element => {
      if (element.classList.contains('no-tooltip')) return false;

      element.addEventListener('mouseenter', event =>
        itemTitle.show(<MouseEvent>event),
      );
      element.addEventListener('mouseleave', () => {
        itemTitle.hide();
      });
    });
  },
  show(event: MouseEvent) {
    const element = (event.target as HTMLElement).closest('div');
    if (!(element instanceof HTMLElement)) return false;
    this.currentEvent = event;
    this.currentTitle = element;
    const item = element.getElementsByTagName('figcaption')[0].innerHTML;
    let price;

    if (this.currentTitle !== item) {
      this.currentTitle = item;
      price = this.currentPrice;
    } else {
      this.currentPrice = itemPrices.findItem(item);
    }
    const itemName = element?.querySelectorAll('figcaption .tooltip_item')[0]
      .innerHTML;
    document.getElementById('tooltip_item_price').innerHTML =
      itemPrices.findItem(itemName) + '';

    const menu = document.getElementById('item_tooltip');
    if (
      menu.children[0].children[0].innerHTML === item &&
      !menu.classList.contains('invisible')
    ) {
      return false;
    }
    // Insert item name at the first li
    menu.children[0].children[0].innerHTML = item;
    const clientRect = element?.getBoundingClientRect();
    menu.classList.remove('invisible');
    // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
    const menuFirstChild = <HTMLElement>menu.children[0];
    const textChild = <HTMLElement>menuFirstChild.children[0];

    const elementImage = element.querySelectorAll('img');

    const minpositionOffset =
      elementImage.length > 0
        ? elementImage[0].clientWidth + 10
        : element.clientWidth;

    const positionTop =
      clientRect.y + window.scrollY - menuFirstChild.clientHeight / 4;
    const positionLeft = clientRect.x + minpositionOffset;
    menuFirstChild.style.top = `${positionTop.toString()}px`;
    menuFirstChild.style.left =
      this.isClippingOutsideScreen(positionLeft, clientRect).toString() + 'px';
    textChild.classList.add('text-center');
  },
  /**
   * If tooltip is clipping outside screen, adjust position to the left
   */
  isClippingOutsideScreen(
    leftPositon: number,
    hostelementRec: DOMRect,
  ): number {
    const tooltipItem = document.getElementById('item_tooltip')?.children[0];
    if (!(tooltipItem instanceof HTMLElement)) return leftPositon;
    const calculatedPosition = leftPositon + this.subtractLeft;
    if (leftPositon + tooltipItem.clientWidth > window.innerWidth) {
      return hostelementRec.x - tooltipItem.clientWidth - 10;
    }

    return calculatedPosition;
  },
  hideItemTooltip() {
    const menu = document.getElementById('item_tooltip');
    menu.classList.add('invisible');
  },
  hide() {
    this.hideItemTooltip();
    this.currentTitle = null;
  },
  resetItemTooltip() {
    if (
      document
        .getElementById('news_content_main_content')
        .querySelectorAll('#item_tooltip').length > 0
    ) {
      document
        .getElementById('inventory')
        .insertBefore(
          document.getElementById('item_tooltip'),
          document
            .getElementById('inventory')
            .querySelectorAll('.inventory_item')[0],
        );
    }
  },
};
