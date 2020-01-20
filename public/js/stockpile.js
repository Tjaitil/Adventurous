    var timeID = [];
    
    var method = false;
    
    function show_menu() {
        console.log("show_menu");
        // Show menu above the item;
        clearTimeout(timeID.pop());
        var element = event.target.closest("div");
        var item = element.getElementsByTagName("figcaption")[0].innerHTML;
        var menu = document.getElementById("stck_menu");
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        var menuTop = element.offsetTop - element.parentNode.scrollTop + 15;
        menu.children[0].style.top = menuTop + "px";
        menu.children[0].style.left = element.offsetLeft + "px";
        var lis = menu.children[0].children;
        // Variable for holding the querySelectorAll("LI");
        var liN;
        if(element.className == 'inventory_item') {
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
        timeID.push(setTimeout(hide_menu, 4000));
    }  
    function hide_menu() {
        document.getElementById("stck_menu").style.visibility = "hidden";
    }
    function withdraw () {
        document.getElementById("stck_menu").style.visibility = "hidden";
        var item = event.target.parentNode.children[0].innerHTML.toLowerCase();
        var quantity = event.target.innerHTML.split(" ")[1];
        if(quantity === 'x') {
            quantity = selectAmount('widthdraw');
        }
        if(quantity == false) {
            return false;
        }
        var data = "model=stockpile" + "&method=updateInventory" + "&item=" + item +
                         "&insert=" + '0' + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            console.log(response[1]);
            if(response[0] !== false) {
                updatePage();
            }
        });
    }
    function insert() {
        document.getElementById("stck_menu").style.visibility = "hidden";
        var item = event.target.parentNode.children[0].innerHTML.toLowerCase();
        var quantity = event.target.innerHTML.split(" ")[1];
        if(quantity === 'x') {
            quantity = selectAmount('insert');
        }
        if(quantity == false) {
            return false;
        }
        var data = "model=stockpile" + "&method=updateInventory" + "&item=" + item + "&insert=" + '1' + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            console.log(response[1]);
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
        var data = "model=stockpile" + "&method=getStockpile";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                document.getElementById("stockpile").innerHTML = response[1];
            }
        });
    }