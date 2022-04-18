    if(document.getElementById("news_content").children[3] != null) {
        var buttons = document.getElementById("fletch").querySelectorAll("button");
        console.log(buttons);
        buttons.forEach(function(element) {
            // Add event for each element
            element.addEventListener('click', fletch);
        });
    }
    function fletch() {
        var amount = event.target.parentElement.children[0].value;
        var item = event.target.closest("tr").querySelectorAll("figcaption")[0].innerHTML.toLowerCase();
        event.target.parentElement.children[0].value = "";
        var data = "model=ArcheryShop" + "&method=fletch" + "&item=" + item  + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('ArhceryShop');
            }       
        });
    }