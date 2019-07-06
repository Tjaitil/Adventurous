    
    function toggleType() {
        var option = document.getElementById("form_select").selectedIndex;
        document.getElementById("form_cont").style.visibility = "visible";
        if(option === 2) {
            document.getElementById("item_b").style.display = "none";
        }
        else {
            document.getElementById("selected").innerHTML = "";
            document.getElementById("item_name").value = "";
            var item_b  = document.getElementById("item_b");
            item_b.style.visibility = "visible";
            item_b.style.display = "block";
        }
    }
    
    function show(element) {
        var divs = ["offers", "my_offers", "history"];
        
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
        clearTimeout(timer);
        timer=setTimeout(checkItem,1000);
    }
    /*function debounce(fn, duration) {
    var timer;
        return function() {
            clearTimeout(timer);
            timer = setTimeout(checkItem, 1000)
        }
    }*/
    
    function checkItem() {
        var query = document.getElementById("item_b").children[1].value;
        var select = document.getElementById("items");
        if(query.length <= 2) {
            var option = document.createElement("OPTION");
            var itemText = document.createTextNode("Search needs to be more than 2 characters");
            option.appendChild(itemText);
            select.appendChild(option);
            return false;
        }
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                while (select.hasChildNodes()) {  
                    select.removeChild(select.firstChild);
                }
                var data = this.responseText.split("|");
                if(data.length > 10) {
                    option = document.createElement("OPTION");
                    itemText = document.createTextNode("Too many results, narrow it down");
                    option.appendChild(itemText);
                    select.appendChild(option);
                }
                else {
                    option = document.createElement("OPTION");
                    select.appendChild(option);
                    for(var i = 0; i < data.length; i++) {
                        option = document.createElement("OPTION");
                        itemText = document.createTextNode(data[i]);
                        option.appendChild(itemText);
                        select.appendChild(option);
                    }
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=Item" + "&method=checkItem" + "&query=" + query);
        ajaxRequest.send();
    }
    
    function selectOpt(element) {
        document.getElementById("item_srch").value = "";
        var itemName = element.options[element.selectedIndex].value;
        var img = document.createElement("IMG");
        img.href = "/public/img/" + itemName;
        img.style = "width: 50px; height: 50px";
        document.getElementById("selected").appendChild(img);
        document.getElementById("item_name").value = itemName;
        
    }
    
    function updatePage(part) {
        console.log("updatePage");
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
    
    function trade(id, element) {
        var amount = element.parentNode.children[1].value;
        if(amount == 0 || amount == null) {
            gameLog("ERROR: Select a amount");
            return false;
        }
        var data = "model=market" + "&method=trade" + "&id=" + id  + "&amount=" + amount; 
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
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
    
    function submit() {
        document.getElementById("offer_form").clear();
        /*document.getElementById("form_cont").style.visibility = "hidden";*/
        updatePage(1);
    }
    
    function cancelOffer(id, element) {
        var data = "model=market" + "&method=cancelOffer" + "&id=" + id;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    updateInventory('market');
                    updatePage(3);
                }
            }
        };
        ajaxRequest.open('POST', 'handlers/handler_p.php');
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }