const menubarToggle = {
    toggled: false,
    addEvent() {
        menubarToggle.toggled = true;
        let figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach(element => element.addEventListener('click', show_menu));
        itemTitle.removeTitleEvent();
    },
    removeEvent() {
        menubarToggle.toggled = false;
        let figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach(element => element.removeEventListener('click', show_menu));
    }
};
function addStockpileActions() {
    let listElements = document.getElementById("stck_menu").querySelectorAll("LI");
    listElements.forEach((element, index) => {
        // First element is the item name, third is the input
        if([0, 3].includes(index)) {
            return;
        }
        element.addEventListener("click", event => stockpileModule.stockpileAction(false, event));
    });
    document.getElementById("stck_menu_custom_amount").addEventListener("keyup", event => {
        event.preventDefault();
        if(event.key === "Enter") {
            stockpileModule.stockpileAction(true, event);
        }
    });
}
function addShowMenuEvent() {
    let figures = document.getElementById("stockpile").querySelectorAll("figure");
    figures.forEach(function(element) {
        element.addEventListener('click', show_menu);
    });
    if(/Safari|Chrome/i.test(navigator.userAgent)) {
        let span = document.getElementsByClassName("item_amount");
        for(var i = 0; i < span.length; i++) {
            span[i].style.left = "-20%";
            span[i].style.display = "block";
        }
    }   
}
function show_menu() {
    // Show menu above the item;
    clearTimeout(timeID.pop());
    let element = event.target.closest("div");
    let menu = document.getElementById("stck_menu");
    if(element.className == 'inventory_item') {
        document.getElementById("inventory").appendChild(menu);         
    }
    else {
        document.getElementById("news_content").appendChild(menu); 
    }
    let item = element.getElementsByTagName("figcaption")[0].innerHTML;
    // Insert item name at the first li
    menu.children[0].children[0].innerHTML = item;
    menu.style.visibility = "visible";
    // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
    let menuTop;
    let lis = menu.children[0].children;
    let elementPos;
    if(element.className == 'inventory_item') {
        for(var i = 1; i < (lis.length - 1); i++) {
            if(i === 3) {
                lis[i].children[0].placeholder = "Insert x";
            } else {
                lis[i].innerHTML = "Insert " + lis[i].innerHTML.split(" ")[1];
            }
        }
        lis[lis.length - 1].innerHTML = "Insert all";
        elementPos = element.getBoundingClientRect();
        if(element.offsetTop + 150 > document.getElementById("stockpile").offsetHeight) {
            menuTop = element.offsetTop - 70;
        } else {
            menuTop = element.offsetTop - 25;
        }
        menu.children[0].style.left = element.offsetLeft + "px";
    }
    else {
        for(var x = 1; x < (lis.length - 1); x++) {
            if(x === 3) {
                lis[x].children[0].placeholder = "Widthdraw x";
            } else {
                lis[x].innerHTML = "Withdraw " + lis[x].innerHTML.split(" ")[1]; 
            }
        }
        lis[lis.length - 1].innerHTML = "Widthdraw all";
        elementPos = element.getBoundingClientRect();
        if(element.offsetTop + 150 > document.getElementById("stockpile").offsetHeight) {
            menuTop = element.offsetTop - 70;
        } else {
            menuTop = element.offsetTop + 85;
        }
        menu.children[0].style.left = element.offsetLeft + "px";
    }
    menu.children[0].style.top = menuTop + "px";
}  
function hideMenu() {
    let menu = document.getElementById("stck_menu");
    menu.style.visibility = "hidden";
    document.getElementById("news_content").appendChild(menu);
}

const stockpileModule = {
    init() {
        document.getElementById("item_tooltip").style.visibility = "hidden";
        menubarToggle.addEvent();
        addShowMenuEvent();
        addStockpileActions();
    },
    stockpileAction(amount = false, event) {
        let element = event.target.closest("div").parentNode;
        let item = document.getElementById("stck_menu").querySelectorAll("li")[0].innerHTML.toLowerCase().trim();
        let quantity;
        let insert;
        if(document.getElementById("stck_menu").closest("#inventory")) {
            insert = "1";
        }
        else {
            insert = "0";    
        }

        if(amount) {
            quantity = document.getElementById("stck_menu_custom_amount").value;
        } else if(event.currentTarget === document.getElementById("stck_menu_all")) {
            quantity = "all";  
        } else {
            quantity = event.target.innerHTML.split(" ")[1];
        }

        hideMenu();

        let data = "model=Stockpile" + "&method=updateInventory" + "&item=" + item +
                            "&insert=" + insert + "&quantity=" + quantity;
        ajaxP(data, response => {
            if(response[0] !== false) {
                let responseText = response[1];
                document.getElementById("stockpile").innerHTML = responseText.html;
                // ShowMenuEvent is added in updateInventory
                updateInventory();
                addShowMenuEvent();
                document.getElementById("stck_menu").style.visibility = "hidden";
                document.getElementById("stck_menu_custom_amount").value = "";
                newsContentSidebar.adjustMainContentHeight();
            }
        });
    },
    onClose() {
        menubarToggle.removeEvent();
        let menu = document.getElementById("stck_menu");
        // If menu is visible remove it
        if(menu) menu.parentElement.removeChild(document.getElementById("stck_menu"));
    }
}
export { 
    stockpileModule as default,
    show_menu
};
