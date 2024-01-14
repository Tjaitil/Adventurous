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
        let parentWidth = this.tooltipElement.parentElement.clientWidth;
        let screenwidth = window.innerWidth;
        this.subtractLeft = parentWidth - screenwidth;
    },

    init(computerDevice) {
        this.tooltipElement = document.getElementById("item_tooltip");
        itemTitle.computerDevice = computerDevice;
        this.adjustForContainerMismatch();
        this.addTitleEvent();
    },
    addTitleEvent() {
        let elements = document.getElementById("inventory").querySelectorAll("figure");
        elements.forEach(element => {
            if (this.computerDevice) {
                element.addEventListener('mouseenter', (event) => itemTitle.show(event));
                element.addEventListener('mouseleave', (event) => itemTitle.hide);
            }
            else {
                element.addEventListener('click', itemTitle.show);
            }
        });
        this.status = true;
    },
    removeTitleEvent() {
        let elements = document.getElementById("inventory").querySelectorAll("figure");
        elements.forEach(element => {
            let clone = element.cloneNode(true);
            element.replaceWith(clone);
        });
        this.status = false;
    },
    addItemClassEvents() {
        // Add events on specific pages
        if (["merchant", "zinsstore"].indexOf(Game.properties.building) !== -1) return false;
        document.getElementById("news_content_main_content");
        let itemDivs = document.getElementById("news_content_main_content").querySelectorAll(".item");
        itemDivs.forEach(element => {
            if (element.classList.contains("no-tooltip")) return false;

            element.addEventListener('mouseenter', event => itemTitle.show(<MouseEvent>event));
            element.addEventListener('mouseleave', () => itemTitle.hide());
        });
    },
    show(event: MouseEvent) {
        let element = (event.target as HTMLElement).closest("div");
        this.currentEvent = event;
        this.currentTitle = element;
        let item = element.getElementsByTagName("figcaption")[0].innerHTML;
        let price;
        
        if (!element.getElementsByTagName("figcaption")[0]) {
            return false;
        }

        if(this.currentTitle !== item) {
            this.currentTitle = item;
            price = this.currentPrice
        } else {
            this.currentPrice = itemPrices.findItem(item);
        }
        document.getElementById("tooltip_item_price").innerHTML = itemPrices.findItem(item) + "";

        let menu = document.getElementById("item_tooltip");
        if (menu.children[0].children[0].innerHTML === item && menu.classList.contains("invisible") === false) {
            return false;
        }
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.classList.remove("invisible");
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        let menuTop;
        let menuFirstChild = <HTMLElement>menu.children[0];
        let textChild = <HTMLElement>menuFirstChild.children[0];

        if (element.className == "inventory_item") {
            menuTop = element.offsetTop + 30;
            menuTop = element.offsetTop - 15;

            menuFirstChild.style.top = menuTop + "px";
            textChild.classList.add("text-center");

            let leftPosition = this.isClippingOutsideScreen(event.clientX);
            if (item.length < 8) {
                menuFirstChild.style.left = leftPosition + "px";
            }
            else {
                menuFirstChild.style.left = leftPosition + "px";
            }
        }
        else {
            let elementParent = element.closest("div");
            elementParent.appendChild(menu);
            menuFirstChild.style.left = 10 + "px";
            menuFirstChild.style.top = 55 + "px";
        }
    },
    isClippingOutsideScreen(leftPositon: number): number {
        let tooltipItem = document.getElementById("item_tooltip").children[0];
        let calculatedPosition = leftPositon + this.subtractLeft;
        if(leftPositon + tooltipItem.clientWidth > window.innerWidth) {
            return calculatedPosition - tooltipItem.clientWidth;
        }

        return calculatedPosition
    },
    hideItemTooltip() {
        let tooltip = document.getElementById("item_tooltip");
        if (tooltip) {
            document.getElementById("item_tooltip").classList.add("invisible");
        }
    },
    hide() {
        this.hideItemTooltip();
        this.currentTitle = null;
    },
    resetItemTooltip() {
        if (document.getElementById("news_content_main_content").querySelectorAll("#item_tooltip").length > 0) {
            document.getElementById("inventory").insertBefore(document.getElementById("item_tooltip"),
                document.getElementById("inventory").querySelectorAll(".inventory_item")[0]);
        }
    }
};