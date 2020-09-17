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
    var generalProperties = {
        screenWidth: 830,
        deviceType: null
    };
    var itemTitle = {
        status : true,
        addTitleEvent : function() {
            let figures = document.getElementById("inventory").querySelectorAll("figure");
            figures.forEach(function(element) {
                // ... code code code for this one element
                element.addEventListener('click', show_title);
            });
            this.status = true;
        },
        removeTitleEvent: function() {
            console.log('removeTitle');
            let figures = document.getElementById("inventory").querySelectorAll("figure");
            figures.forEach(function(element) {
                // ... code code code for this one element
                  element.removeEventListener('click', show_title);
            });
            this.status = false;
        }
    };
    function eventTest() {
        show_title();
    }
    function addShowTitle() {
        var figures = document.getElementById("inventory").querySelectorAll("figure");
        figures.forEach(function(element) {
            // ... code code code for this one element
              element.addEventListener('click', show_title);
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
            if(window.location.href.indexOf("stockpile") == -1) {
                addShowTitle();    
            }
        }
        console.log(document.getElementById("inv_toggle_button").style.visibility);
        if(window.screen.width < 830) {
            console.log('loAD');
            let inventory = document.getElementById("inventory");
            inventory.style.visibility = "hidden";
            inventory.style.transition = "width 0.5s";
            inventory.style.transitionTimingFunction = "ease-out";
            document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
        }
        clock();
        checkMessages();
        sidebar.addClickEvent();
        sidebar.addAdventure();
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
        secondLog(message);
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
    function secondLog(message) {
        let div = document.getElementById("log_2");
        div.innerHTML = message;
        div.style.opacity = 1;
        div.style.top = window.pageYOffset + 50  + "px";
        
        
        setTimeout(function() {
            console.log('settimeout');
            let div = document.getElementById("log_2");
            div.style.opacity = 0;
            div.style.top = "0px";
        }, 2000);
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
    function openNews(content, sidebar = false) {
        document.getElementById("news_content_main_content").innerHTML = "";
        var newsDiv = document.getElementById("news_content");
        /*while(newsDiv.childNodes.length > 2) {
            newsDiv.removeChild(newsDiv.lastChild);
        }*/
        document.getElementById("news").style.visibility = "visible";
        document.getElementById("news_content").style.visibility = "visible";
        if(typeof content == 'object') {
            if(Object.keys(content).length > 1) {
                for(var i = 0; i < Object.keys(content).length; i++) {
                    document.getElementById("news_content_main_content").appendChild(content[i]);
                }
            }
        }
        else {
            document.getElementById("news_content_main_content").innerHTML += content;
        }
        let siteNoSidebar = ["Stockpile"];
        if(sidebar == true && siteNoSidebar.indexOf(document.getElementsByClassName("page_title")[0].innerText) === -1) {
            newsContentSidebar.adjustSidebar();
        }
        else {
            console.log('style width');
            document.getElementById("news_content_main_content").style.width = "94%";
        }
    }
    function closeNews() {
        var newsDiv = document.getElementById("news_content_main_content");
        newsDiv.innerHTML = "";
        /*while(newsDiv.childNodes.length > 2) {
            newsDiv.removeChild(newsDiv.lastChild);
        }*/
        console.log(newsDiv);
        news.style = "visibility: hidden;";
        document.getElementById("news_content").style.visibility = "hidden";
        if(typeof inBuilding !== 'undefined') {
            // Render the player outside building
            /*renderPlayer(0, 40);*/
            inBuilding = false;
        }
        if(selectItemEvent.selectItemStatus === true) {
            selectItemEvent.removeSelectEvent();
        }
        if(typeof menubarToggle != undefined) {
            menubarToggle.removeEvent();
        }
        if(itemTitle.status === false) {
            itemTitle.addTitleEvent();
        }
    }
    var inventorySidebarMob = {
        toggleInventory() {
            console.log('toggleInventory');
            let inventory = document.getElementById("inventory");
            console.log(inventory);
            if(inventory.style.visibility === "hidden") {
                console.log('hlelo');
                inventory.style.width = "50%";
                inventory.style.visibility = "visible";
                console.log(inventory);
            }
            else {
                inventory.style.width = "10%";
                inventory.style.visibility = "hidden";
                console.log(inventory);
            }
        }
    };
    var newsContentSidebar = {
        adjustSidebar() {
            if(document.getElementById("news_content_main_content").style.width === "100%") {
                document.getElementById("news_content_main_content").style.width = "75%";
                document.getElementById("news_content_side_panel").style.width = "25%";
            }
            document.getElementById("news_content_side_panel").innerHTML = "";
            var divChildren = document.getElementById("news_content_main_content").children;
            let exceptions = ["put_on", "mission_enabled", "current_mission"];
            let buttonCount = 0;
            for(var i = 0; i < divChildren.length; i++) {
                if(divChildren[i].tagName === 'DIV' && exceptions.indexOf(divChildren[i].id) == -1) {
                    let button = document.createElement("button");
                    // Get text from div id and remove underscore and Uppercase character of each word;
                    console.log(divChildren[i].id);
                    let text = document.createTextNode(jsUcWords(underscoreTreatment(divChildren[i].id, false)));
                    button.appendChild(text);
                    document.getElementById("news_content_side_panel").appendChild(button);
                    divChildren[i].style.position = "absolute";
                    divChildren[i].style.visibility = "hidden";
                    divChildren[i].style.width = "100%";
                    buttonCount++;
                }
            }
            if(buttonCount == 1) {
                document.getElementById("news_content_main_content").querySelectorAll("div")[0].style.visibility = "visible";
                document.getElementById("news_content_main_content").style.width = "100%";
                document.getElementById("news_content_side_panel").style.display = "none";
            }
            this.addEvent();
        },
        showContent() {
            let activeButton = event.target.innerText;
            let buttons = document.getElementById("news_content_side_panel").querySelectorAll("button");
            for(var i = 0; i < buttons.length; i++) {
                if(buttons[i].innerText == activeButton) {
                    document.getElementById(underscoreTreatment(activeButton, true).toLowerCase()).style.visibility = "visible";
                }
                else {
                    document.getElementById(underscoreTreatment(buttons[i].innerText, true).toLowerCase()).style.visibility = "hidden";
                }
            }
        },
        addEvent() {
            let buttons = document.getElementById("news_content_side_panel").querySelectorAll("button");
            buttons.forEach(function(element) {
                // Add eventListener to each node
                element.addEventListener('click', newsContentSidebar.showContent);
            });
        }
    };
    function underscoreTreatment(string, addUnderscore) {
        let result = string.search("_");
        let editedString;
        if(result != -1 && addUnderscore === false) {
            editedString = string.replace("_", " ");
        }
        else if(result == -1 && addUnderscore === true) {
            editedString = string.replace(" ", "_");
        }
        else {
            return string;
        }
        return editedString;
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
                document.getElementById("alert_submit").addEventListener("click", function() {
                    changeProfiency();
                    closeNews();
                });
                break;
        }
        content[1] = subButton;
        content[2] = cancButton;
        openNews(content);
        
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
                if(document.getElementsByClassName("page_title")[0].innerText == "Stockpile") {
                    var buttons = document.getElementById("inventory").querySelectorAll("div");
                    buttons.forEach(function(element) {
                    // ... code code code for this one element
                    element.addEventListener('click', show_menu);
                    });
                    itemTitle.removeTitleEvent();
                }
                else {
                    addShowTitle();
                }
                if(selectItemEvent.selectItemStatus == true) {
                    selectItemEvent.addSelectEvent();
                }
                document.getElementById("inv_toggle_button").addEventListener("click", inventorySidebarMob.toggleInventory);
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
        var menuTop;
        // If element is inside inventory or stockpile
        menuTop = element.offsetTop + 30;
        console.log(menuTop);
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
        console.log(element.parentNode);
        /*if(["stockpile", "inventory"].indexOf(element.parentNode.id) != -1) {
            //menuTop = element.offsetTop + element.parentNode.scrollTop + 50;
            menuTop = element.offsetTop + 30;
            console.log(menuTop);
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
        }
        else {
            var elementPos = element.getBoundingClientRect();
            menuTop = document.documentElement.scrollTop + elementPos.y - 164;
            console.log("element.offsetTop: " + element.offsetTop);
            console.log("element.parentNode.scrollTop" + element.parentNode.scrollTop);
            console.log("html srollTop:" + document.documentElement.scrollTop);
            console.log(menuTop);
            console.log(document.documentElement);
            console.log(element.getBoundingClientRect());
            menu.children[0].style.top = menuTop + "px";
            menu.children[0].style.left = element.offsetLeft + 10 + "px";
            console.log(menu);
        }*/
        
        setTimeout(hide_title, 4000, element);
    }  
    function hide_title(element) {
        /*element.style.visibility = "hidden";*/
        if(data[1] == true) {
            var div_button = data[2].parentElement.children[0];
            div_button.style = "visibility: hidden";
        }
    }
    function getCountdown() {
        var data = "model=countdown" + "&method=getCountdown";    
        ajaxG(data, function(response) {
            if(response[0] != false) {
                
                document.getElementById("tab_2").innerHTML = "";        
            }
        });
    }
    function jsUcfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
    function jsUcWords(str) {
        console.log(str);
        return str.replace(/\w\S*/g, function(txt){
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
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
    var sidebar = {
        sidebarElement: document.getElementById("sidebar"),
        sidebarToggled: false,
        addClickEvent() {
            let sidebarTabs = document.getElementById("sidebar").querySelectorAll("button");
            sidebarTabs.forEach(function(element) {
                // ... code code code for this one element
                if(element.previousElementSibling == null) {
                    return;
                }
                element.addEventListener('click', function() {
                        sidebar.showTab(sidebarCheck = true);
                });
            });
            this.sidebarElement = document.getElementById("sidebar");
        },
        toggleSidebar() {
            if(this.sidebarToggled === false) {
                this.sidebarElement.style.visibility  = "visible";
                this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.40 + "px";
                this.sidebarToggled = true;
                document.getElementById("sidebar_button_toggle").style.visibility = "visible";
            }
            else {
                sidebar.showTab(sidebarCheck = false);
                this.sidebarElement.style.width = document.getElementsByTagName("section")[0].clientWidth * 0.12 + "px";
                this.sidebarToggled = false;
                if(window.screen.width > 830) {
                    document.getElementById("sidebar_button_toggle").style.visibility = "hidden";
                }
                else {
                    setTimeout(function() {
                        document.getElementById("sidebar").style.visibility = "hidden";}, 600);
                }
            }
        },
        showTab(sidebarCheck) {
            console.log(sidebarCheck);
            if(sidebar.sidebarToggled === false && sidebarCheck === true) {
                sidebar.toggleSidebar();    
            }
            let newActiveTab = event.target.innerText;
            console.log(newActiveTab);
            let tabs = document.getElementById("sidebar").querySelectorAll(".sidebar_tab");
            let tabNames = ["Adventure", "Countdowns", "Diplomacy", "Skills"];
            for(var i = 0; i < tabNames.length; i++) {
                if(tabNames[i] === newActiveTab) {
                    tabs[i].style.visibility = "visible"; 
                }
                else {
                    tabs[i].style.visibility = "hidden";
                }
            }
        }
    };
    newLevel = {
        skillElement: null,
        skillData: null,
        highLightIndex: 0,  
        updateNewLevel: function(skillData) {
            newLevel.skillData = skillData;
            document.getElementById("skills").querySelectorAll(".skill_level")[newLevel.elementIndex[skillData[0]] - 1].innerHTML =
                    skillData[1];
            gameLog(skillData[0] + "leveled up to " + skillData[1]);
            
        },
        searchString: function(responseText) {
            if(responseText.search("levelup") != -1) {
                let index = response[1].search("levelup");
                newLevel.findSkill(responseText.slice(index));
            }
        },
        findSkill: function(string) {
            let skillData = string.split("|");
            let skills = ["farmer", "miner", "trader", "warrior"];
            if(skills.indexOf(skillData[1]) == -1) {
                return;
            }
            else {
                newLevel.updateNewLevel([skillData[1], skillData[2]]);
            }
        },
        elementIndex: {
            adventurer: 0,
            farmer: 1,
            miner: 2,
            trader: 3,
            warriors: 4
        },
        skillHighlight: function(element) {
            console.log(element.style.backgroundColor);
            if(element.style.backgroundColor === "" || element.style.backgroundColor == "rgb(242, 230, 217)") {
                element.style.backgroundColor = "#f8f2ec";
            }
            else {
                element.style.backgroundColor = "#f2e6d9";
            }
        }
        
    };
    function getAdventure() {
        let building = "Adventures";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                let responseText = this.responseText;
                console.log(responseText);
                document.getElementById("sidebar").getElementsByTagName("div")[0].innerHTML = responseText;
            }
        };
        ajaxRequest.open('GET', "handlers/handler_v.php?" + "&building=" + building);
        ajaxRequest.send();
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