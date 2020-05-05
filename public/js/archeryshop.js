    window.onload = function () {
        var buttons = document.getElementById("fletch").querySelectorAll("button");
        buttons.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                fletch();
            });
        });
    };
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
    
    function hello() {
        console.log('external js');
    }