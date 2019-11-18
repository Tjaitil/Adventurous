
    function make(item, element) {
        var parent = element.parentNode;
        var amount = parent.children[0].value;
        parent.children[0].value = '';
        
        if(amount == 0) {
            alert("Please enter a valid quantity");
            return false;
        }
        var data = "model=Bakery" + "&method=make" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                updateInventory('bakery');
            }
        });
    }