
    function withdraw (element, quantity) {
        if(quantity === 'x') {
            quantity = selectAmount('widthdraw');
        }
        if(quantity == false) {
            return false;
        }
        var class_name = element.parentNode.parentNode;
        var fig_text = class_name.children[1].children[1].innerHTML.split("x ");
        var item = fig_text[1];
        var data = "model=stockpile" + "&method=updateInventory" + "&item=" + item +
                         "&insert=" + '0' + "&quantity=" + quantity;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.length > 0) {
                    gameLog(this.responseText);
                }
                else {
                    updatePage();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function insert(element, quantity) {
        if(quantity === 'x') {
            quantity = selectAmount('insert');
        }
        if(quantity == false) {
            return false;
        }
        var class_name = element.parentNode.parentNode;
        var item = class_name.children[1].children[1].innerHTML.trim();
        var data = "model=stockpile" + "&method=updateInventory" + "&item=" + item + "&insert=" + '1' + "&quantity=" + quantity;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.length > 0) {
                    gameLog(this.responseText);
                }
                else {
                    updatePage();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php?");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
        
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