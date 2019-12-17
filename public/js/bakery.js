
    function make(item, element) {
        var parent = element.parentNode;
        var amount = parent.children[0].value;
        parent.children[0].value = '';
        
        if(amount == 0) {
            alert("Please enter a valid quantity");
            return false;
        }
        var data = "model=Bakery" + "&method=makeMeal" + "&item=" + item + "&amount=" + amount;
        ajaxP(data, function(response) {
            console.log(response[0]);
            if(response[0] != false) {
                updateInventory('bakery');
            }
        });
    }
    function lol() {
        /*ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_p.php");
        ajaxRequest.send();*/
        var data = "model=Bakery" + "&method=makeMeal" + "&item=" + "askaks" + "&amount=" + 1;
        ajaxP(data, function(response) {
            
        });
    }
    
    