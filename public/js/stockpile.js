    var timeID = [];
    
    var method = false;
    // Check if news_content_main_content -> children[2] has gotten content from game.js -> game.fetchBuilding()
    if(document.getElementById("news_content").children[2] != null) {
        menubarToggle.addEvent();
        /*let figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', show_menu());
        });*/ 
    }
    menubarToggle = {
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
            document.getElementById("stockpile").appendChild(menu);
        }
        var item = element.getElementsByTagName("figcaption")[0].innerHTML;
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        var menuTop;
        console.log('parentNode.ScrollTop: ' + element.parentNode.scrollTop);
        console.log('elementOffsetTop: ' + element.offsetTop);
        var lis = menu.children[0].children;
        // Variable for holding the querySelectorAll("LI");
        var liN;
        if(element.className == 'inventory_item') {
            menuTop = element.offsetTop + 15;
            for(var i = 1; i < (lis.length - 1); i++) {
                lis[i].innerHTML = "Insert " + lis[i].innerHTML.split(" ")[1];
            }
            if(method == 'withdraw' || method == false) {
                //Change addEventListener function
                method = "insert";
                liN = menu.querySelectorAll("LI");
                liN.forEach(function(element, index) {
                    // First element is the item name
                    if(index === 0) {
                        return;   
                    }
                    // Remove event for each element
                    element.removeEventListener('click', withdraw);
                    // Add event for each element;
                    element.addEventListener('click', insert);
                });
            }
        }
        else {
            menuTop = element.offsetTop - element.parentNode.scrollTop + 15;
            for(var x = 1; x < (lis.length - 1); x++) {
                lis[x].innerHTML = "Withdraw " + lis[x].innerHTML.split(" ")[1]; 
            }
            if(method == 'insert' || method == false) {
                //Change addEventListener function
                method = "withdraw";
                liN = menu.querySelectorAll("LI");
                liN.forEach(function(element, index) {
                    // First element is the item name
                    if(index === 0) {
                        console.log(element);
                        return;   
                    }
                    // Remove event for each element
                    element.removeEventListener('click', insert);
                    // Add event for each element;
                    element.addEventListener('click', withdraw);
                });
            }
        }
        menu.children[0].style.top = menuTop + "px";
        menu.children[0].style.left = element.offsetLeft + "px";
        /*timeID.push(setTimeout(hideMenu, 4000));*/
    }  
    function hideMenu() {
        let menu = document.getElementById("stck_menu");
        menu.style.visibility = "hidden";
        document.getElementById("news_content").appendChild(menu);
    }
    function withdraw () {
        hideMenu();
        var item = event.target.parentNode.children[0].innerHTML.toLowerCase();
        var quantity = event.target.innerHTML.split(" ")[1];
        if(quantity === 'x') {
            quantity = selectAmount('widthdraw');
        }
        if(quantity == false) {
            return false;
        }
        var data = "model=Stockpile" + "&method=updateInventory" + "&item=" + item +
                         "&insert=" + '0' + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updatePage();
            }
        });
    }
    function insert() {
        hideMenu();
        var item = event.target.parentNode.children[0].innerHTML.toLowerCase();
        var quantity = event.target.innerHTML.split(" ")[1];
        if(quantity === 'x') {
            quantity = selectAmount('insert');
        }
        if(quantity == false) {
            return false;
        }
        var data = "model=Stockpile" + "&method=updateInventory" + "&item=" + item + "&insert=" + '1' + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
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
        updateInventory('stockpile');
        var data = "model=Stockpile" + "&method=getData";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                document.getElementById("stockpile").innerHTML = response[1];
            }
        });
    }