    window.onload = function () {
        var div = document.getElementById("offers");
        var inputs = div.getElementsByTagName("input");
        var buttons = div.querySelectorAll("button");
        for(var i = 0; i < inputs.length; i++) {
            if(inputs[i].getAttribute("type") == 'hidden') {
                offers[inputs[i].parentElement.parentElement.nodeName + i] = inputs[i].value;
            }
        }
        buttons.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                trade();
            });
        });
        var figures = document.getElementById("my_offers").querySelectorAll("figure");
        figures.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                fetchItem();
            });
        });
        buttons = document.getElementById("my_offers").querySelectorAll("button");
        buttons.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                cancelOffer();
            });
        });
        document.getElementById("item_srch").addEventListener('keyup', chk_me);
        document.getElementById("s_item").addEventListener('keyup', chk_me);
    };
    
    var offers = {};
    function toggleType() {
        var option = document.getElementById("form_select").selectedIndex;
        document.getElementById("form_cont").style.display = "block";
        if(option === 2) {
            document.getElementById("item_b").style.display = "none";
        }
        else {
            document.getElementById("item_name").value = "";
            var item_b  = document.getElementById("item_b");
            item_b.style.visibility = "visible";
            item_b.style.display = "block";
        }
    }
    function show(element) {
        var divs = ["offers", "my_offers", "history"];
        
        document.getElementById("form_cont").style.display = "none";
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == element) {
                document.getElementById(divs[i]).style = "display: inline";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
            }
        }
    }
    var timer;
    function chk_me(){
        console.log(event.target);
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
        var query = document.getElementById("item_b").children[1].value;
        if(query.length === 0) {
            return;
        }
        var select = document.getElementById("items");
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
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                var data = this.responseText.split("|");
                console.log(data);
                if(data.length > 10) {
                    itemText.nodeValue = "Too many results, narrow it down";
                    option.appendChild(itemText);
                    select.appendChild(option);
                }
                else if(data.length > 0 && this.responseText.length > 0) {
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
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=Item" + "&method=checkItem" + "&query=" + query);
        ajaxRequest.send();
    }
    function selectOpt(element) {
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
        var button =document.getElementById("sch_button");
        button.style.display = "initial";
        button.addEventListener("click", function() {
            var table = document.getElementById("offers").children[0];
            table.removeChild(table.lastChild);
            table.children[1].style.display = "initial";
            button.style.display = "none";
            document.getElementById("s_item").value = "";
        });
        var data = "model=market" + "&method=searchOffers" + "&item=" + item;
        ajaxG(data, function(response) {
            if(response[0] !== false) {
                console.log(response[1]);
                var table = document.getElementById("offers").children[0];
                table.children[1].style.display = "none";
                if(table.children[2] != undefined) {
                    table.children[2].innerHTML = response[1];
                }
                else {
                    table.innerHTML += response[1];
                }
            }
        });
    }
    function newOffer() {
        var form = document.getElementById("offer_form");
        var JSON_data = JSONForm(form);
        
        var data = "model=market" + "&method=newOffer" + "&JSON_data=" + JSON_data;
        ajaxP(data, function(response) {
            console.log(response[1]);
            if(response[0] != false) {
                document.getElementById("offer_form").reset();
                document.getElementById("form_cont").style.display = "none";
                updatePage(1);
            }
        });
    }
    function updatePage(part) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                /*var data = this.responseText.split("||");
                var table = document.getElementById("offers").childNodes[1];
                var tr = document.createElement("TR");
                for(var i = 0; i < data.length; i++) {
                    datapieces = data[i].split("|");
                    for(var x = 0; x < datapieces.length; x++) {
                        var element = document.createElement("TD");
                        element.innerHTML = datapieces[i];
                        if((x + 1) % 3 == 0) {
                            var img = document.createElement("IMG");
                            img.src = "../public/images/gold.jpg";
                            img.setAttribute("class", "gold");
                            element.innerHTMl += img;
                        }
                        tr.appendChild(element);
                    }
                    table.appendChild(tr); }
                console.log(this.responseText);
                document.getElementById("test").innerHTML += this.responseText;*/
                var data = this.responseText.split("#");
                switch(part) {
                    case 1:
                        document.getElementById("offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[0];
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[1];
                        break;
                    case 2:
                        document.getElementById("offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[0];
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[1];
                        document.getElementById("history").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[2];
                        break;
                    case 3:
                        document.getElementById("my_offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = data[0];
                        break;
                }
                /*if(part == 1) {
                    console.log(this.responseText);
                    document.getElementById("offers").getElementsByTagName("TABLE")[0].children[1].innerHTML = this.responseText;
                }
                else if(part == 2) {
                    var data = this.responseText.split("#");
                    var divs = ['offers', 'my_offers', 'history'];
                    
                    for(var i = 0; i < divs.length; i++) {
                        document.getElementById(div[i]).getElementsByTagName("TABLE").children[1].innerHTML = data[i];
                    }
                }
                else {
                    
                }*/
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=market" + "&method=getData" + "&part=" + part);
        ajaxRequest.send();
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
        var data = "model=market" + "&method=trade" + "&id=" + id  + "&amount=" + amount; 
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    updateInventory('market');
                    updatePage(2);
                }
            }
        };
        ajaxRequest.open('POST', 'handlers/handler_p.php');
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
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
        var data = "model=market" + "&method=cancelOffer" + "&id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('market');
                updatePage(3);
            } 
        });
    }
    function fetchItem() {
        var id = event.target.closest("TR").children[0].children[0].value;
        var data = "model=market" + "&method=fetchItem" + "&id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory('market');
                updatePage(3);
            }
        });
    }