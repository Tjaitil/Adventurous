    /*if(document.getElementById("inventory") != null) {
        addSelectEvent(false);
    }*/
    function select() {
        let element = event.target.closest("figure");
        console.log(element);
        var figure = element.cloneNode(true);
        /*img.removeAttribute("onclick");*/
        figure.children[0].style.height = "50px";
        figure.children[0].style.width = "50px";
        figure.children[1].style.visibility = "hidden";
        /*figure.className = "item";*/
        var parent = document.getElementById("selected");
        parent.innerHTML = "";
        parent.appendChild(figure);
        switch(document.getElementsByClassName("page_title")[0].innerText) {
            case 'Armory':
                toggleOption();
                break;
            case 'Tavern':
                getHealingAmount(element.querySelectorAll("figcaption")[0].innerHTML);
                break;
            default:
                
                break;
        }
    }
    function select_i() {
        var element = event.target.closest("figure");
        toggleOfferType();
        var item = element.children[1].innerHTML.toLowerCase().trim();
        if(item === 'gold') {
            gameLogger.addMessage("ERROR: You cannot sell gold!");
            gameLogger.logMessages();
            return false;
        }
        document.getElementById("item_name").value = jsUcWords(item);
        let img = element.cloneNode(true);
        img.removeChild(img.children[1]);
        img.removeAttribute("onclick");
        let parent = document.getElementById("selected");
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
                if(selectItemEvent.page === "Market") {
                    element.addEventListener('click', select_i);
                }
                else if(selectItemEvent.page === "Merchant") {
                    element.addEventListener('click', selectTrade);
                }
                else {
                    element.addEventListener('click', select);
                }
            });
        },
        removeSelectEvent() {
            selectItemEvent.selectItemStatus = false;
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function(element) {
                if(selectItemEvent.page === "Market") {
                    element.removeEventListener('click', select_i);
                }
                else if(selectItemEvent.page === "Merchant") {
                    element.removeEventListener('click', selectTrade);
                }
                else {
                    element.removeEventListener('click', select);
                }
            });
        }
    };
    selectItemConv = {
        eventStatus: false,
        addEvent() {
            /*if(document.getElementById("conversation_container").style.visibility !== "visible") {
                return false;
            }*/
            eventStatus = true;
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function(element) {
                element.addEventListener('click', selectItemConv.selectItem);
            });
            highlightInventory.set();
        },
        removeEvent() {
            eventStatus = false;
            let figures = document.getElementById("inventory").querySelectorAll('figure');
            figures.forEach(function(element) {
                element.removeEventListener('click', selectItemConv.selectItem);
            });
            highlightInventory.clear();
        },
        selectItem() {
            let figure = event.target.closest("figure");
            let item = figure.children[1].innerHTML.toLowerCase();
            conversation.getNextLine(item);
        }
    };
    function selectedCheck(amount_r = true) {
        if(document.getElementById("selected").getElementsByTagName("figure").length == 0) {
            gameLogger.addMessage("Please select a valid item");
            gameLogger.logMessages();
            return false;
        }
        var div = document.getElementById("selected");
        let item = document.getElementById("selected").querySelectorAll("figcaption")[0].innerHTML.toLowerCase().trim(); 
        // amount_r is variable that opens up for checking only item or item and amount
        if(amount_r === true) {
            var amount = document.getElementById("selected_amount").value;
            if(amount == 0) {
                gameLogger.addMessage("Please select a valid amount");
                gameLogger.logMessages();
                return false;
            }
            return [item, amount];
        }
        else {
            return [item];
        }
    }