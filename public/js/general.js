    function clock() {
        var today = new Date();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();
        minutes = checkZero(minutes);
        seconds = checkZero(seconds);
        document.getElementById("clock").innerHTML =
        hours + ":" + minutes + ":" + seconds;
        var id = setTimeout(clock, 1000);
    }
    function checkZero(i) {
        if (i < 10) {i = "0" + i;}
        return i;
    }
    window.addEventListener("load", function() {
        var log = document.getElementById("log");
        if(log != null) {
            log.scrollTop = log.scrollHeight - log.clientHeight;
        }
        clock();
    });
    
    function displayNav() {
        var visibility = document.getElementById("nav_2").children[0].style.visibility;
        if(visibility == 'visible') {
            document.getElementById("nav_2").children[0].style.visibility = "hidden";
        }
        else {
            document.getElementById("nav_2").children[0].style.visibility = "visible";
        }
    }
    function addZero(i) {
        if (i < 10) {
          i = "0" + i;
        }
        return i;
    }
    function gameLog(message) {
        if(message.search("\\[") == -1) {
            var d = new Date();
            var time = "[" + addZero(d.getHours()) + ":" + addZero(d.getMinutes()) + ":" + addZero(d.getSeconds()) + "] ";
            message = time + message;
        }
        var tr = document.createElement("TR");
        var td = document.createElement("TD");
        var table = document.getElementById("game_messages");
        tr.appendChild(td);
        td.innerHTML = message;
        var log = document.getElementById("log");
        var isScrolledToBottom = log.scrollHeight - log.clientHeight <= log.scrollTop + 1;
        table.appendChild(tr);
        // scroll to bottom if isScrolledToBottom
        if(isScrolledToBottom) {
          log.scrollTop = log.scrollHeight - log.clientHeight;
        }
    } 
    function getgMessage() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                gameLog(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_ses.php?variable=game_message");
        ajaxRequest.send();
    }
    /*window.addEventListener("load", getgMessage, false);*/
    function openNews(news) {
        console.log(news);
        document.getElementById("news").style.visibility = "visible";
        document.getElementById("content").style.visibility = "visible";
        console.log(typeof news);
        if(typeof news == 'object') {
            document.getElementById("content").appendChild(news);
        }
        else {
            document.getElementById("content").innerHTML += news;
        }
    }
    function closeNews() {
        console.log('hello');
        var news = document.getElementById("news");
        news.innerHTML = "";
        news.style = "visibility: hidden;";
        document.getElementById("content").style.visibility = "hidden";
    }
    function get_xp(skill, element) {
        var tooltip = element.children[2];
        tooltip.style.right = "-30%";
        if(tooltip.style.visibility == "visible") {
            tooltip.style.visibility = "hidden";
            return false;
        }
        else {
            if(tooltip.innerHTML.indexOf("C") != -1) {
                tooltip.style.visibility = "visible";
                console.log("no AJAX");
            }
            else {
                ajaxRequest = new XMLHttpRequest();
                ajaxRequest.onload = function () {
                    if(this.readyState == 4 && this.status == 200) {
                        var data = this.responseText.split("|");
                        data.shift();
                        var skillName = skill.charAt(0).toUpperCase() + skill.slice(1);
                        /*element.title = skillName + '\n' + "Current xp: " + data[0] + '\n' + "Next level: " + data[1];*/
                        tooltip.innerHTML = skillName + '</br>' + "Current xp: " + data[0] + '</br>' + "Next level: " + data[1];
                        tooltip.style.visibility = "visible";
                    }
                };
                ajaxRequest.open('GET', "handlers/handler_ses.php?variable=" + skill);
                ajaxRequest.send();
            }
        }
    }
    function show_xp(skill, xp) {
        var elements = {
            adventurer: 0,
            farmer: 1,
            miner: 2,
            trader: 3,
            warriors: 4
        };
        console.log(elements[skill]);
        var element = document.getElementById("skills").children[elements[skill]].children[3];
        element.innerHTML = "+" + xp;
        setTimeout(hide_xp, 2000, element);
    }
    function hide_xp(element) {
        element.innerHTML = "";
    }   
    function updateInventory(page) {
        /*inventory_items = inventory.split("||");
        inventory_items.pop();
        var class_length = document.getElementsByClassName("inventory_item").length;
        if(inventory_items.length > class_length ) {
            var clone = document.getElementsByClassName("inventory_item")[0].cloneNode(true);
            clone.setAttribute("class", "inventory_item");
            document.getElementById("inventory").appendChild(clone);
        }
        if(inventory_items.length < class_length) {
            document.getElementsByClassName("inventory_item")[class_length - 1].remove();
        }
        for(var x = 0; x < inventory_items.length; x++) {
            var inventory_item = inventory_items[x].split("|");
            document.getElementsByClassName("inventory_item")[x].children[0].children[1].innerHTML =
            inventory_item[1] + " x " + inventory_item[0];
        }*/
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                document.getElementById("inventory").innerHTML = this.responseText;
            }
        };
        ajaxRequest.open('GET', "handlers/handlerf.php?file=inventory" + "&page=" + page);
        ajaxRequest.send();
    }
    var timeID = [];
    function show_title(element, buttons) {
        var tooltip = element.children[1];
        tooltip.style.right = "30%";
        tooltip.style.visibility = "visible";
        if(buttons == true) {
            var div_button = element.parentElement.children[0];
            if(div_button.style.visibility == "visible") {
                div_button.style = "visibility: hidden";
            }
            else {
                div_button.style = "visibility: visible";
            }
        }
        var data = [tooltip, buttons, element];
        setTimeout(hide_title, 4000, data);
    }  
    function hide_title(data) {
        data[0].style.visibility = "hidden";
        if(data[1] == true) {
            var div_button = data[2].parentElement.children[0];
            div_button.style = "visibility: hidden";
        }
    }
    function jsUcfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function jsUcWords(str) {
        return str.replace(/\w\S*/g, function(txt){
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }
    function selectedCheck() {
        if(document.getElementById("selected").getElementsByTagName("figure").length == 0) {
            gameLog("Please select a valid item");
            return false;
        }
        var quantity = document.getElementById("quantity").value;
        if(quantity == 0) {
            gameLog("Please select a valid amount");
            return false;
        }
        return true;
    }
    function JSONForm(form) {
        var form_data = {};
        for(var i = 0; i < form.length; i++) {
            switch(form[i].tagName) {
                case 'DIV':
                    for(var x = 0; x < form[i].children.length; x++) {
                        if(form[i].children[x].tagName != 'INPUT') {
                            continue;
                        }
                    form_data[form[i].children[x].name] = form[i].children[x].value;
                    }
                    break;
                case 'INPUT':
                    if(form[i].type == 'checkbox' && form[i].checked === true) {
                        form_data[form[i].name] = form[i].value;
                    }
                    form_data[form[i].name] = form[i].value;
                    break;
                case 'SELECT':
                    form_data[form[i].name] = form[i].children[form[i].selectedIndex].value;
                    break;
                default:
                    break;
                
            }
            if(form[i].tagName == 'DIV') {
                
            }
            else if(form[i].tagName != 'INPUT') {
                continue;
            }
            form_data[form[i].name] = form[i].value;
        }
        return JSON.stringify(form_data);
    }
    function responseRet(response) {
        console.log("responseRet");
        return response;
    }
    function ajaxG(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    if(log == true) {
                        gameLog(this.responseText);
                    }
                    callback([false, this.responseText]);
                }
                else {
                    callback([true, this.responseText]);
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?" + data);
        ajaxRequest.send();
    }
    function ajaxP(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    if(log == true) {
                        gameLog(this.responseText);
                    }
                    callback([false, this.responseText]);
                }
                else {
                    callback([true, this.responseText]);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }