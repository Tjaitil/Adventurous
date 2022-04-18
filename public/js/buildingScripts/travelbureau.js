    
    function buyItem (item, shop) {
        this.item = item;
        this.shop = shop;
        let data = "model=TravelBureau" + "&method=buyItem" + "&item=" + item + "&shop=" + shop;
        ajaxP(data, function(response) {
           if(response[0] != false) {
                updateStock();
           }
        });
    }
    function updateStock() {
        let data = "model=TravelBureau" + "&method=getData";
        ajaxG(data, function(response) {
            let responseText = response[1];
            document.getElementById("cart_shop").children[1].innerHTML = responseText[1].html;
            updateInventory();
        });
    }