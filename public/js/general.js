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
    function addShowTitle() {
        var figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach(function(element) {
            // ... code code code for this one element
              element.addEventListener('click', function() {
                  show_title();
              });
        });   
    }
    window.addEventListener("load", function() {
        var log = document.getElementById("log");
        if(log != null) {
            log.scrollTop = log.scrollHeight - log.clientHeight;
        }
        if(document.getElementById("inventory") != null) {
            document.getElementById("inventory").addEventListener("scroll", function() {
               console.log("scroll");
               document.getElementById("item_tooltip").style.visibility = "hidden";
            });
            addShowTitle();
        }
        clock();
        checkMessages();
    });
    function checkMessages() {
        var data = "model=Messages" + "&method=checkMessages";
        ajaxG(data, function(response) {
           console.log(response[1]);
            if(response[1] > 0) {
                document.getElementById("nav").querySelectorAll(".top_but")[5].children[0].innerHTML += ' ' + '(' + response[1] + ')';
            }
        });
    }
    function displayNav() {
        var visibility = document.getElementById("nav_2").children[1].style.visibility;
        if(visibility == 'visible') {
            document.getElementById("nav_2").children[1].style.visibility = "hidden";
        }
        else {
            document.getElementById("nav_2").children[1].style.visibility = "visible";
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
    function openNews(content) {
        document.getElementById("news").style.visibility = "visible";
        document.getElementById("news_content").style.visibility = "visible";
        if(typeof content == 'object') {
            
            if(Object.keys(content).length > 1) {
                for(var i = 0; i < Object.keys(content).length; i++) {
                    document.getElementById("news_content").appendChild(content[i]);
                }
            }
        }
        else {
            document.getElementById("news_content").innerHTML += content;
        }
    }
    function closeNews() {
        var news = document.getElementById("news");
        news.innerHTML = "";
        news.style = "visibility: hidden;";
        document.getElementById("news_content").style.visibility = "hidden";
    }
    function alertMessage(page, pIdentifier = false) {
        // pIdentifer is used to identify which content to append if this function is used multiple times on the same page
        var subButton = document.createElement("button");
        subButton.appendChild(document.createTextNode("Submit"));
        subButton.setAttribute("id", "alert_submit");
        var cancButton = document.createElement("button");
        cancButton.appendChild(document.createTextNode("Cancel"));
        cancButton.setAttribute("id", "alert_cancel");
        
        var content = {};
        switch(page) {
            case 'citycentre':
                var link = document.createElement("a");
                link.appendChild(document.createTextNode("here"));
                link.setAttribute("href", "/gameguide/profiency");
                console.log(link);
                var p = document.createElement("p");
                var message =
                'Beware that changing profiency may result in lowering levels </br> and no access to profiency specific activites';
                var message2 = '</br> Read more on </br>';
                var message3 = "Are you sure you want to continue?";
                p.innerHTML = message + message2;
                p.appendChild(link);
                p.innerHTML += '</br>' + message3;
                content[0] = p;
                break;
        }
        content[1] = subButton;
        content[2] = cancButton;
        openNews(content);
        document.getElementById("alert_submit").addEventListener("click", function() {
            changeProfiency();
            closeNews();
        });
        document.getElementById("alert_cancel").addEventListener("click", closeNews);
        document.getElementById("cont_exit").addEventListener("click", closeNews);
    }
    function get_xp(skill, element) {
        var tooltip = element.children[1];
        tooltip.style.right = "-30%";
        if(tooltip.style.visibility == "visible") {
            tooltip.style.visibility = "hidden";
            return false;
        }
        else if(skill == 'adventurer') {
            tooltip.innerHTML = skill.charAt(0).toUpperCase() + skill.slice(1);
            tooltip.style.visibility = "visible";
        }
        else {
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
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
    function show_xp(skill, xp) {
        var elements = {
            adventurer: 0,
            farmer: 1,
            miner: 2,
            trader: 3,
            warriors: 4
        };
        console.log(elements[skill]);
        var element = document.getElementById("skills").children[elements[skill]].children[2];
        element.innerHTML = "+" + xp;
        setTimeout(hide_xp, 2000, element);
    }
    function hide_xp(element) {
        element.innerHTML = "";
    }   
    function updateInventory(page, addSelect = false) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                document.getElementById("inventory").innerHTML = this.responseText;
                if(addSelect !== false) {
                    addSelectEvent();
                }
                addShowTitle();
            }
        };
        ajaxRequest.open('GET', "handlers/handlerf.php?file=inventory" + "&page=" + page);
        ajaxRequest.send();
    }
    var timeID = [];
    function show_title() {
        var element = event.target.closest("div");
        console.log(element);
        var item = element.getElementsByTagName("figcaption")[0].innerHTML;
        var menu = document.getElementById("item_tooltip");
        // Insert item name at the first li
        menu.children[0].children[0].innerHTML = item;
        menu.style.visibility = "visible";
        // Declare menu top by measuring the positon from top of parent and also if inventory/stockpile is scrolled
        var menuTop = element.offsetTop - element.parentNode.scrollTop + 50;
        menu.children[0].style.top = menuTop + "px";
        if(item.length < 8) {
            menu.children[0].style.left = element.offsetLeft +  20 + "px";
            menu.children[0].children[0].style.width = "50px";
            menu.children[0].children[0].style.textAlign = "center";
            
        }
        else {
            menu.children[0].style.left = element.offsetLeft + 10 + "px";
            menu.children[0].children[0].style.width = "auto";
        }
        
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
    function selectedCheck(quantity_r = true) {
        if(document.getElementById("selected").getElementsByTagName("figure").length == 0) {
            gameLog("Please select a valid item");
            return false;
        }
        var div = document.getElementById("selected");
        var figure = div.querySelectorAll("figure")[0];
        var item = figure.children[1].innerHTML.toLowerCase();
        
        // amount_r is variable that opens up for checking only item or item and amount
        if(quantity_r === true) {
            var quantity = document.getElementById("quantity").value;
            if(quantity == 0) {
                gameLog("Please select a valid amount");
                return false;
            }
            return [item, amount];
        }
        else {
            return [item];
        }
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
                    if(form[i].name.length == 0) {
                        break;
                    }
                    if(form[i].type == 'checkbox' && form[i].checked === true) {
                        form_data[form[i].name] = form[i].value;
                    }
                    form_data[form[i].name] = form[i].value;
                    break;
                case 'SELECT':
                    if(form[i].name.length > 0) {
                        form_data[form[i].name] = form[i].children[form[i].selectedIndex].value;
                        break;
                    }
                    break;
                default:
                    break;   
            }
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
    function ajaxJS(data, callback, log = true) {
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
        ajaxRequest.open('GET', "handlers/handler_js.php?" + data);
        ajaxRequest.send();
    }
    function ajaxP(data, callback, log = true) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
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