    var offers = {};
    if(document.getElementById("news_content").children[2] != null) {
        let div = document.getElementById("offers");
        let inputs = div.getElementsByTagName("input");
        /*for(var i = 0; i < inputs.length; i++) {
            if(inputs[i].getAttribute("type") == 'hidden') {
                offers[inputs[i].parentElement.parentElement.nodeName + i] = inputs[i].value;
            }
        }*/
        // Add event to search button to get back to offers
        document.getElementById("sch_button").addEventListener("click", function() {
            var table = document.getElementById("offers").children[0];
            table.removeChild(table.lastChild);
            table.children[2].style.display = "";
            document.getElementById("sch_button").style.display = "none";
            document.getElementById("s_item").value = '';
        });
        // Add event to button in offer_types
        document.getElementById("offer_types").querySelectorAll("button")[0].addEventListener("click", toggleOfferType);
        document.getElementById("offer_types").querySelectorAll("button")[1].addEventListener("click", toggleOfferType);
        
        document.getElementById("select_item").addEventListener("change", selectOpt);
        
        offersListeners();
        
        intervalUpdate();
        document.getElementById("item_srch").addEventListener('keyup', chk_me);
        document.getElementById("s_item").addEventListener('keyup', chk_me);
        selectItemEvent.addSelectEvent();
    }
    function intervalUpdate() {
        let x = setInterval(function() {
            // Check if offer div is still avaliable, if not the user has moved on
            if(document.getElementById("offers") == null) {
                clearInterval(x);
            }
            else {
                updatePage(2);
            }
        }, 10000);
    }
    function tradeEvent() {
        console.log(document.getElementById("offers").children[0].children[2]);
        let buttons = document.getElementById("offers").children[0].children[2].querySelectorAll("button");
        buttons.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                trade();
            });
        });
    }
    function offersListeners() {
        tradeEvent();
        var itemClass = document.querySelectorAll(".item");
        itemClass.forEach(function(element) {
            // Add event to figure inside item class
            element.querySelectorAll("figure")[0].addEventListener('click', function() {
                show_title();
            });
        });
        // Add even to boxItem class to retrieve item
        var boxItems = document.querySelectorAll(".box_item");
        boxItems.forEach(function(element) {
            element.addEventListener('click', function() {
                fetchItem();
            });
        });
        buttons = document.getElementById("my_offers").querySelectorAll("button");
        buttons.forEach(function(element) {
            if(element.innerText !== 'Cancel offer') {
                element.addEventListener('click', function() {
                    toggleOfferType();
                });
            }
            else {
                element.addEventListener('click', function() {
                    cancelOffer();
                });
            }
        });    
    }
    function toggleOfferType() {
        // Toggle type of offer between sell and buy
        var div = document.getElementById("form_cont");
        div.style.display = "block";
        var select = document.getElementById("form_select");
        var type = event.target.innerText;
        var item_b  = document.getElementById("item_b");
        if(type === "Sell" || type.length == 0) {
            item_b.style.display = "none";
            document.getElementById("offer_types").querySelectorAll("button")[0].style.backgroundColor = "";
            document.getElementById("offer_types").querySelectorAll("button")[1].style.backgroundColor = "#e4cdb4";
        }
        else {
            document.getElementById("item_name").value = "";
            item_b.style.visibility = "visible";
            item_b.style.display = "block";
            document.getElementById("offer_types").querySelectorAll("button")[0].style.backgroundColor = "#e4cdb4";
            document.getElementById("offer_types").querySelectorAll("button")[1].style.backgroundColor = "";
        }
        // Adjust the height of news_content_main_content
        newsContentSidebar.adjustMainContentHeight();
    }
    var timer;
    function chk_me(){
        if(event.target.id == "item_srch") {
            clearTimeout(timer);
            timer=setTimeout(checkItem, 1000);
        }
        else {
            clearTimeout(timer);
            timer=setTimeout(searchOffers, 1000);
        }
    }
    function checkItem() {
        console.log('check_item');
        var query = document.getElementById("item_srch").value;
        if(query.length === 0) {
            return;
        }
        var select = document.getElementById("select_item");
        while (select.lastChild) {
            select.removeChild(select.lastChild);
        }
        var option = document.createElement("OPTION");
        var itemText = document.createTextNode("");
        if(query.length <= 2) {
            itemText.nodeValue = "Search needs to be more than 2 characters";
            option.appendChild(itemText);
            select.appendChild(option);
            return false;
        }
        itemText.nodeValue = "Searching...";
        option.appendChild(itemText);
        select.appendChild(option);
        var data = "model=Item" + "&method=checkItem" + "&query=" + query;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                data = response[1].split("|");
                console.log(data);
                if(data.length > 10) {
                    itemText.nodeValue = "Too many results, narrow it down";
                    option.appendChild(itemText);
                    select.appendChild(option);
                }
                else if(data.length > 0 && response[1].length > 0) {
                    select.children[0].innerHTML = " ";
                    for(var i = 0; i < data.length; i++) {
                        option = document.createElement("OPTION");
                        option.innerHTML = data[i];
                        select.appendChild(option);

                    }
                }
                else {
                    itemText.nodeValue = "No items found";
                    option.appendChild(itemText);
                    select.appendChild(option);
                }
            }
        });
    }
    function selectOpt() {
        let element = event.target;
        document.getElementById("item_srch").value = "";
        var itemName = element.options[element.selectedIndex].value;
        var div = document.getElementById("selected");
        if(div.children[0] == undefined) {
            var img = document.createElement("IMG");
            img.href = "/public/img/" + itemName + ".png";
            img.style = "width: 50px; height: 50px";
            div.appendChild(img);
            document.getElementById("item_name").value = itemName;     
        }
        else {
            div.children[0].href = "public/img/" + itemName + ".png";
            document.getElementById("item_name").value = itemName;    
        }
    }
    function searchOffers() {
        var item = document.getElementById("s_item").value;
        if(item.length === 0) {
            return false;
        }
        var button = document.getElementById("sch_button");
        button.style.display = "initial";
        var data = "model=Market" + "&method=searchOffers" + "&item=" + item;
        ajaxG(data, function(response) {
            if(response[0] !== false) {
                var table = document.getElementById("offers").children[0];
                table.children[2].style.display = "none";
                if(table.children[3] != undefined) {
                    table.children[3].innerHTML = response[1];
                }
                else {
                    table.innerHTML += response[1];
                }
            }
        });
    }
    function newOffer() {
        var form = document.getElementById("offer_form");
        var JSON_data = JSON.parse(JSONForm(form));
        
        if(JSON_data.length > 3) {
            JSON_data.offerType = "Buy";
        }
        else {
            JSON_data.offerType = "Sell";
        }
        console.log(JSON_data);
        var data = "model=Market" + "&method=newOffer" + "&JSON_data=" + JSON.stringify(JSON_data);
        ajaxP(data, function(response) {
            if(response[0] != false) {
                console.log(response);
                updateInventory();
                document.getElementById("offer_form").reset();
                document.getElementById("form_cont").style.display = "none";
                updatePage(1);
            }
        });
    }
    function updatePage(part) {
        var data = "model=Market" + "&method=getData" + "&part=" + part;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("#");
                switch(part) {
                    case 1:
                        document.getElementById("offers").getElementsByTagName("TABLE")[0].children[2].innerHTML = data[0];
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[2].innerHTML = data[1];
                        break;
                    case 2:
                        console.log(document.getElementById("offers").querySelectorAll("#item_tooltip"));
                        if(document.getElementById("offers").querySelectorAll("#item_tooltip").length > 0) {
                            console.log('helloasda');
                            document.getElementById("inventory").appendChild(document.getElementById("item_tooltip"));
                            console.log(document.getElementById("item_tooltip"));
                            document.getElementById("item_tooltip").style.visibility = "hidden";
                        }
                        document.getElementById("offers").getElementsByTagName("TABLE")[0].children[2].innerHTML = data[0];
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[2].innerHTML = data[1];
                        document.getElementById("history").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[2];
                        break;
                    case 3:
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[2].innerHTML = data[0];
                        break;
                }
                offersListeners();
            }
        });
    }
    function trade() {
        var amount = event.target.parentElement.children[1].value;
        var id = event.target.parentElement.children[2].value;
        /*var tr = event.target.closest("TR");
        var i = 0;
        while( (tr = tr.previousSibling) != null ) {
            i++;
        }
        if(offers["TR" + (i+1)] != id) {
            gameLog("ERROR: Please try again");
            return false;
        }*/
        
        if(amount == 0 || amount == null) {
            gameLog("ERROR: Select a amount");
            return false;
        }
        var data = "model=Market" + "&method=trade" + "&id=" + id  + "&amount=" + amount;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                updateInventory('market');
                updatePage(2);
            }       
        });
    }
    function offerCheck(element, id) {
        var tr = element.parentElement.parentElement;
        var i = 0;
        while( (tr = tr.previousSibling) != null ) {
            i++;
        }
        if(offers["TR" + (i+1)] != id) {
            gameLog("ERROR: Please try again");
            return false;
        }
    }
    function submit() {
        document.getElementById("offer_form").reset();
        /*document.getElementById("form_cont").style.visibility = "hidden";*/
        updatePage(1);
    }
    function cancelOffer() {
        var id = event.target.closest("TR").children[0].children[0].value;
        var data = "model=Market" + "&method=cancelOffer" + "&id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('market');
                updatePage(3);
            } 
        });
    }
    function fetchItem() {
        var id = event.target.closest("TR").children[0].children[0].value;
        var data = "model=Market" + "&method=fetchItem" + "&id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('market');
                updatePage(3);
            }
        });
    }