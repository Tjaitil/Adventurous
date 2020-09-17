    /*if(document.getElementById("inventory") != null) {
        addSelectEvent(false);
    }*/
    function select() {
        let element = event.target.closest("figure");
        var figure = element.cloneNode(true);
        /*img.removeAttribute("onclick");*/
        figure.children[0].style.height = "50px";
        figure.children[0].style.width = "50px";
        figure.children[1].style.visibility = "hidden";
        figure.className = "item";
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(figure);
        if(document.getElementsByClassName("page_title")[0].innerText == "Armory") {
            toggleOption();
        }
    }
    function select_i() {
        var element = event.target.closest("figure");
        toggleType();
        var item = element.children[1].innerHTML.toLowerCase().trim();
        if(item === 'gold') {
            gameLog("ERROR: You cannot sell gold!");
            return false;
        }
        document.getElementById("item_name").value = jsUcWords(item);
        var img = element.cloneNode(true);
        img.removeChild(img.children[1]);
        img.removeAttribute("onclick");
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(img);
    }
    selectItemEvent = {
        selectItemStatus: false,
        pages: null,
        addSelectEvent() {
            selectItemEvent.selectItemStatus = true;
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function(element) {
                selectItemEvent.page = document.getElementsByClassName("page_title")[0].innerText;
                if(selectItemEvent.page !== "Market") {
                    element.addEventListener('click', select);
                }
                else {
                    element.addEventListener('click', select_i);
                }
            });
        },
        removeSelectEvent() {
            selectItemEvent.selectItemStatus = false;
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function(element) {
                if(selectItemEvent.page !== "Market") {
                    element.removeEventListener('click', select);
                }
                else {
                    element.removeEventListener('click', select_i);
                }
            });
        }
    };
    function selectedCheck(amount_r = true) {
        if(document.getElementById("selected").getElementsByTagName("figure").length == 0) {
            gameLog("Please select a valid item");
            return false;
        }
        var div = document.getElementById("selected");
        var figure = div.querySelectorAll("figure")[0];
        var item = figure.children[1].innerHTML.toLowerCase();
        
        // amount_r is variable that opens up for checking only item or item and amount
        if(amount_r === true) {
            var amount = document.getElementById("selected_amount").value;
            if(amount == 0) {
                gameLog("Please select a valid amount");
                return false;
            }
            return [item, amount];
        }
        else {
            return [item];
        }
    }