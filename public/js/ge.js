
    registerOffer() {
        var inputs = document.getElementById("register_offer").querySelectorAll("input");
        var item = inputs[0].value;
        var price_ea = inputs[1].value;
        
        var data = "model=ge" + "&method=registerOffer" + "&item=" + item + "&price_ea=" + price_ea;
        ajaxP(data, function(response) {
           if(response[0] != false) {
                updateOffers();
                inputs[0].value = "";
                inputs[1].value = "";
           }
        });
    }
    
    updateOffers() {
        
        var data = "model=ge" + "&method=getOffers";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                // Oppdatere tabell     
            }
        });
    }
    
    
    getOffer() {
        
    }