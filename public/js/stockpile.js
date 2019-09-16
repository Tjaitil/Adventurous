    var timeID = [];
    
    var method = false;
    
    function show_menu() {
        // Show menu above the item;
        clearTimeout(timeID.pop());
        var element = event.target.closest("div");
        var item = element.getElementsByTagName("figcaption")[0].innerHTML;
        var menu = document.getElementById("stck_menu");
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        var menuTop = element.offsetTop - element.parentNode.scrollTop + 15;
        menu.children[0].style.top = menuTop + "px";
        menu.children[0].style.left = element.offsetLeft + "px";
        var lis = menu.children[0].children;
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
                    if(index === 0) {
                        return;   
                    }
                     // Add event for each element;
                     element.addEventListener('click', function() {
                         insert();
                     });
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
                console.log("change");
                liN = menu.querySelectorAll("LI");
                liN.forEach(function(element, index) {
                    if(index === 0) {
                        return;   
                    }
                     // Add event for each element;
                     element.addEventListener('click', function() {
                         withdraw();
                     });
                });
            }
        }
        setTimeout(hide_menu, 4000);
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
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                document.getElementById("stockpile").innerHTML = this.responseText;
                /*var stockpile_items = this.responseText.split("||");
                stockpile_items.pop();
                for(var i = 0; i < stockpile_items.length; i++) {
                    var stockpile_item = stockpile_items[i].split("|");
                    document.getElementsByClassName("stockpile_item")[i].children[1].children[1].innerHTML =
                    stockpile_item[0] + " x " + stockpile_item[1];
                }*/
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=stockpile" + "&method=getStockpile");
        ajaxRequest.send();
        /* 
        inventory_items = inventory.split("||");
        inventory_items.pop();
        var class_length = document.getElementsByClassName("inventory_item").length;
        if(inventory_items.length > class_length ) {
            console.log("clone");
            var clone = document.getElementById("hidden").cloneNode(true);
            clone.removeAttribute("id");
            clone.setAttribute("class", "inventory_item");
            document.getElementById("inventory").appendChild(clone);
        }
        if(inventory_items.length < class_length) {
            document.getElementsByClassName("inventory_item")[class_length - 1].remove();
        }
        for(var x = 0; x < inventory_items.length; x++) {
            var inventory_item = inventory_items[x].split("|");
            console.log(inventory_item);
            document.getElementsByClassName("inventory_item")[x].children[1].children[1].innerHTML =
            inventory_item[0] + " x " + inventory_item[1];
        }*/
    }