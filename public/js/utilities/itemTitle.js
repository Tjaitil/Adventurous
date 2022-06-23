// Container for item title for items
const itemTitle = {
    status: true,
    currentTitle: null,
    computerDevice: true,
    init(computerDevice) {
        itemTitle.computerDevice = computerDevice;
        this.addTitleEvent();
    },
    addTitleEvent() {
        let elements = document.getElementById("inventory").querySelectorAll("figure");
        elements.forEach(element => {
            if (this.computerDevice) {
                element.addEventListener('mouseenter', itemTitle.show);
                element.addEventListener('mouseleave', itemTitle.hide);
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
            if (this.computerDevice) {
                element.removeEventListener('mouseenter', itemTitle.show);
                element.removeEventListener('mouseleave', itemTitle.hide);
            }
            else {
                element.removeEventListener('click', itemTitle.show);
            }
        });
        this.status = false;
    },
    addItemClassEvents() {
        // Add events on specific pages
        if(["merchant", "zinsstore"].indexOf(game.properties.building) !== -1) return false;
        let itemDivs = document.getElementById("news_content_main_content").querySelectorAll(".item");
        itemDivs.forEach(element => {
            element.addEventListener('mouseenter', () => itemTitle.show());
            element.addEventListener('mouseleave', () => itemTitle.hide());
        });
    },
    show() {
        let element = event.target.closest("div");
        this.currentTitle = element;
        let item = element.getElementsByTagName("figcaption")[0].innerHTML;
        let menu = document.getElementById("item_tooltip");
        if(menu.children[0].children[0].innerHTML === item && menu.style.visibility !== "hidden") {
            return false;
        }
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        let menuTop;
        document.getElementById("tooltip_item_price").innerHTML = itemPrices.findItem(item);
        if (element.className == "inventory_item") {
            document.getElementById("inventory").insertBefore(menu,
                document.getElementById("inventory").querySelectorAll(".inventory_item")[0]);
            // menuTop = element.offsetTop + 30;
            menuTop = element.offsetTop - 15;
            menu.children[0].style.top = menuTop + "px";
            menu.children[0].children[0].style.textAlign = "center";
            if (item.length < 8) {
                menu.children[0].style.left = element.offsetLeft + 20 + "px";
            }
            else {
                menu.children[0].style.left = element.offsetLeft + 10 + "px";
            }
        }
        else {
            let elementParent = element.closest("div");
            let firstChild = elementParent.children[0];
            elementParent.appendChild(menu);
            menu.children[0].style.left = 10 + "px";
            menu.children[0].style.top = 55 + "px";
        }
    },
    hide() {
        document.getElementById("item_tooltip").style.visibility = "hidden";
        this.currentTitle = null;
    },
    resetItemTooltip() {
        if(document.getElementById("news_content_main_content").querySelectorAll("#item_tooltip").length > 0) {
            document.getElementById("inventory").insertBefore(document.getElementById("item_tooltip"),
            document.getElementById("inventory").querySelectorAll(".inventory_item")[0]);
        }
    }
};