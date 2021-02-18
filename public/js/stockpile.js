    
    var timeID = [];
    
    var method = false;
    var menubarToggle = {
        toggled: false,
        addEvent: function() {
            menubarToggle.toggled = true;
            var buttons = document.getElementById("inventory").querySelectorAll("div");
            buttons.forEach(function(element) {
                // ... code code code for this one element
                element.addEventListener('click', show_menu);
            });
            itemTitle.removeTitleEvent();
        },
        removeEvent: function() {
            menubarToggle.toggled = false;
            var buttons = document.getElementById("inventory").querySelectorAll("div");
            buttons.forEach(function(element) {
                // ... code code code for this one element
                element.removeEventListener('click', show_menu);
            });
        }
    };
    function addStockpileActions() {
        let listElements = document.getElementById("stck_menu").querySelectorAll("LI");
        listElements.forEach(function(element, index) {
            // First element is the item name
            if(index === 0) {
                return;   
            }
            element.addEventListener("click", stockpileAction);
        });
    }
    function addShowMenuEvent() {
        var figures = document.getElementById("stockpile").querySelectorAll("figure");
        figures.forEach(function(element) {
            // ... code code code for this one element
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
    // Check if news_content_main_content -> children[2] has gotten content from game.js -> game.fetchBuilding()
    if(document.getElementById("news_content").children[2] != null) {
        menubarToggle.addEvent();
        addShowMenuEvent();
        addStockpileActions();
    }
    function show_menu() {
        console.log("show_menu");
        // Show menu above the item;
        clearTimeout(timeID.pop());
        var element = event.target.closest("div");
        var menu = document.getElementById("stck_menu");
        console.log(menu);
        if(element.className == 'inventory_item') {
            document.getElementById("inventory").appendChild(menu);         
        }
        else {
            document.getElementById("news_content").appendChild(menu); 
        }
        var item = element.getElementsByTagName("figcaption")[0].innerHTML;
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        var menuTop;
        /*console.log('parentNode.ScrollTop: ' + element.parentNode.scrollTop);
        console.log('elementOffsetTop: ' + element.offsetTop);*/
        var lis = menu.children[0].children;
        
        let elementPos;
        if(element.className == 'inventory_item') {
            for(var i = 1; i < (lis.length - 1); i++) {
                lis[i].innerHTML = "Insert " + lis[i].innerHTML.split(" ")[1];
            }
            lis[lis.length - 1].innerHTML = "Insert all";
            elementPos = element.getBoundingClientRect();
            /*console.log(element.offsetTop);
            console.log(elementPos);
            console.log(screen.height);
            console.log(elementPos.top + 145);*/
            if(element.offsetTop + 150 > document.getElementById("stockpile").offsetHeight) {
                console.log('1');
                menuTop = element.offsetTop - 70;
            }
            /*else if(screen.height < elementPos.top + 145) {
                console.log('menudown');
                menuTop = elementPos.top - 70;
                console.log(menuTop);
            }*/
            else {
                console.log('3');
                menuTop = element.offsetTop + 15;
            }
            menu.children[0].style.left = element.offsetLeft + "px";
        }
        else {
            let newsContent = document.getElementById("news_content");
            for(var x = 1; x < (lis.length - 1); x++) {
                lis[x].innerHTML = "Withdraw " + lis[x].innerHTML.split(" ")[1]; 
            }
            lis[lis.length - 1].innerHTML = "Widthdraw all";
            elementPos = element.getBoundingClientRect();
            /*console.log(element.offsetTop);
            console.log(elementPos);
            console.log(screen.height);
            console.log(elementPos.top + 145);*/
            if(element.offsetTop + 150 > document.getElementById("stockpile").offsetHeight) {
                console.log('1');
                menuTop = element.offsetTop - 70;
            }
            /*else if(screen.height < elementPos.top + 145) {
                console.log('menudown');
                menuTop = elementPos.top - 70;
                console.log(menuTop);
            }*/
            else {
                console.log('3');
                menuTop = element.offsetTop + 85;
            }
            menu.children[0].style.left = element.offsetLeft + "px";
        }
        menu.children[0].style.top = menuTop + "px";
        /*timeID.push(setTimeout(hideMenu, 4000));*/
    }  
    function hideMenu() {
        let menu = document.getElementById("stck_menu");
        menu.style.visibility = "hidden";
        document.getElementById("news_content").appendChild(menu);
    }
    function stockpileAction() {
        hideMenu();
        let element = event.target.closest("div").parentNode;
        let item = event.target.parentNode.children[0].innerHTML.toLowerCase();
        let quantity = event.target.innerHTML.split(" ")[1];
        let insert;
        console.log(document.getElementById("stck_menu").querySelectorAll("LI")[1].innerHTML);
        console.log(document.getElementById("stck_menu").querySelectorAll("LI")[1].innerHTML.indexOf("Insert"));
        if(document.getElementById("stck_menu").querySelectorAll("LI")[1].innerHTML.indexOf("Insert") != -1) {
            insert = "1";
            if(quantity === 'x') {
                quantity = selectAmount('insert');
            }
        }
        else {
            insert = "0";    
            if(quantity === 'x') {
                quantity = selectAmount('withdraw');
            }
        }
        let data = "model=Stockpile" + "&method=updateInventory" + "&item=" + item +
                         "&insert=" + insert + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                console.log(response[1]);
                updatePage();
            }
        });
    }
    function selectAmount(type) {
        var amount = prompt("Select a number to withdraw " + type);
        if(amount == false) {
            return false;
        }
        else if(isNaN(amount) == true || amount.search(",") != -1) {
            gameLog("ERROR: Please insert a valid number!");
            return false;
        }
        else {
            return amount;
        }
    }
    function updatePage() {
        var data = "model=Stockpile" + "&method=getData";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
                document.getElementById("stockpile").innerHTML = response[1];
                updateInventory('stockpile');
                addShowMenuEvent();
                document.getElementById("stck_menu").style.visibility = "hidden";
            }
        });
    }