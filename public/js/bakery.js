
    function make(item, element) {
        var parent = element.parentNode;
        var quantity = parent.children[0].value;
        parent.children[0].value = '';
        
        if(quantity == 0) {
            alert("Please enter a valid quantity");
            return false;
        }
        var data = "model=Bakery" + "&method=make" + "&item=" + item + "&quantity=" + quantity;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1 ) {
                    gameLog(this.responseText);
                    return false;
                }
                updateInventory('bakery');
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }