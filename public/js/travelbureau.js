    
    function buyItem (item, shop) {
        this.item = item;
        this.shop = shop;
        var data = "model=travelbureau" + "&method=buyItem" + "&item=" + item + "&shop=" + shop;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    gameLog(this.responseText);
                    updateStock();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function updateStock() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("#");
                document.getElementById("horse_shop").children[1].innerHTML = data[0];
                document.getElementById("cart_shop").children[1].innerHTML = data[1];
                updateInventory();
                /*
                var carts = data[1];
                var horseShop = data[0].split("||");
                horseShop.pop();
                var horseShopItem = horseShop[0].split("|");
                for(var i = 0; i < horseShopItem.length; i++) {
                    document.getElementById("horse_shop").rows[1].cells[i].innerHTML = horseShopItem[i];
                }
                var cartShop = carts.split("||");
                cartShop.pop();
                for(var x = 0; x < cartShop.length; x++) {
                    var cartShopItem = cartShop[x].split("|");
                    for(var y = 0; y < 5; y++) {
                        document.getElementById("cart_shop").rows[x + 1].cells[y].innerHTML = cartShopItem[y];
                    }
                }*/
            }
        };
        ajaxRequest.open("GET", "handlers/handler_js.php?model=travelbureau" + "&method=getData");
        ajaxRequest.send();
    }