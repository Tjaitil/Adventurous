    
    function buyItem (item, shop) {
        this.item = item;
        this.shop = shop;
        var data = "model=TravelBureau" + "&method=buyItem" + "&item=" + item + "&shop=" + shop;
        ajaxP(data, function(response) {
           if(response[0] != false) {
                gameLog(response[1]);
                updateStock();
           }
        });
    }
    
    function updateStock() {
        var data = "model=TravelBureau" + "&method=getData";
        ajaxG(data, function(response) {
            var data = response[1].split("#");
            document.getElementById("horse_shop").children[1].innerHTML = data[0];
            document.getElementById("cart_shop").children[1].innerHTML = data[1];
            updateInventory();
        });
    }