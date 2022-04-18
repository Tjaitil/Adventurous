    scriptLoader.loadScript(['jsonForm'], 'utility');
    
    if(document.getElementById("news_content").children[2] != null) {
        if(document.getElementById("provide") != null && document.getElementById("provide").children.length > 0) {
            document.getElementById("provide").querySelectorAll(":scope > button")[0].addEventListener('click', provide);
            var divs = document.querySelectorAll(".warriors");
            if(divs.length > 0) {
                divs.forEach(function(element) {
                    element.addEventListener('click', function() {
                        check();
                    });
                    element.querySelectorAll("button")[0].addEventListener('click', flipWarriorCard);
                });
            }
            else {
                selectItemEvent.addSelectEvent();
            }
        }
        if(document.getElementById("time") != null) {
            document.getElementById("time").addEventListener("load", getCountdown());
        }
        if(document.getElementById("invite") != null) {
            var buttons = document.getElementById("invite").querySelectorAll("button");
            for(var i = 0; i < buttons.length; i++) {
                if(buttons[i].innerText.indexOf("Toggle") != -1) {
                    buttons[i].addEventListener("click", toggleInvite);
                    break;
                }
            }
            document.getElementById("hire-citizen").addEventListener("click", hireCitizen);
        }
        // Add event to the warriors
        addWarriorEvents();
        
        // If div with id people does not exists there is no active adventure
        if(document.getElementById("people") != null) {
            document.getElementsByName("leave_adventure")[0].addEventListener("click", leaveAdventure);
        }
        document.getElementsByName("start_adventure")[0].addEventListener("click", newAdventure);
    }
    function getCountdown() {
        var data = "model=Adventures" + "&method=getCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                let time = responseText['adventure_countdown'] * 1000;
                let status = responseText['adventure_status'];
                let x = setInterval (function() {
                    let now = new Date().getTime();
                    let distance = time - now;
                    let days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0 && status === "1"){
                        clearInterval(x);
                        /*document.getElementById("current_adventure").removeChild(document.getElementById("adv_start"));*/
                        let btn = document.createElement("BUTTON");
                        let t = document.createTextNode("Fetch report");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateAdventure);
                        document.getElementById("report").appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
                        document.getElementsByName("leave_adventure")[0].style.display = "none";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "";
                    }
                }, 1000);
            }
        });
    }
    var figures1 = document.getElementsByTagName("FIGURE");
    /*var figures = document.getElementsByTagName("FIGURE");
    for(var i = 0; i < figures.length; i++) {
        console.log(figures[i]);
        figures[i].addEventListener('click', handle.bind(figures[i]));
    }*/
    function newAdventure() {
        var form = document.getElementById("data_form");
        if(!form.reportValidity()) {
            gameLogger.addMessage("Please fill out all required inputs in the form");
            gameLogger.logMessages();
            return false;
        }
        var JSON_data = JSONForm(form);
        let data = "model=SetAdventure" + "&method=newAdventure" + "&JSON_data=" + JSON_data;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                inputHandler.fetchBuilding("adventures");
            }
        });
        
    }
    function handle(figure) {
        console.log(figure);
        select(figures[i]);
        show_title(figures[i], false);
    }
    function checkLevel() {
        var select = document.getElementById("diff_select");
        var difficulty = select[select.selectedIndex].value;
        var difficulties = {
            medium: 5.0,
            hard: 12
        };
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.indexOf("ERROR") != -1) {
                    return false;    
                }
                if(this.responseText < difficulties[difficulty]) {
                    document.getElementById("new_adventure").querySelectorAll("button")[0].disabled = true;
                    document.getElementById("diff_select").setCustomValidity("Adventurer respect too low for this difficulty");
                }
                else {
                    document.getElementById("new_adventure").querySelectorAll("button")[0].disabled = false;
                    document.getElementById("diff_select").setCustomValidity("");
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_ses.php?variable=adventurer_respect");
        ajaxRequest.send();
    }
    function toggleInvite() {
        var data = "model=SetAdventure" + "&method=toggleInvite";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                var div = document.getElementById("invite");
                
                var p = div.querySelectorAll("p");                
                for(var i = 0; i < p.length; i++) {
                    if(p[i].innerText.indexOf("invite") != -1) {
                        p[i].innerHTML = "Leader invite only: " + response[1];
                        break;
                    }
                }
                if(response[1] == 'off') {
                    for(i = 0; i < 4; i++) {
                        div.children[i].style.display = "none";
                    }
                }
                else {
                    for(var x = 0; x < 4; x++) {
                        div.children[x].style.display = "inline";
                    }
                }
            }
        });
    }
    function hireCitizen() {
        let selectElement = document.getElementById("adventure-citizen-role");
        let role = selectElement.options[selectElement.selectedIndex].value;
        if(!role.length > 0) {
            gameLogger.addMessage("You need to select a role");
            gameLogger.logMessages();
            return false;
        }
        let people = document.getElementById("people").querySelectorAll("figcaption");
        // Check if role has been filled
        if(people[selectElement.selectedIndex - 1].innerHTML !== "None") {
            gameLogger.addMessage("That role is already filled");
            gameLogger.logMessages();
            return false;
        } else if(selectElement.selectedIndex - 1 === 3) {
            gameLogger.addMessage("Citizens can't be hired for the warrior role");
            gameLogger.logMessages();
            return false;
        }
        
        let data = "model=SetAdventure" + "&method=hireCitizen" + "&role=" + role;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                inputHandler.fetchBuilding("adventures");
                updateInventory();
            }
        });
    }
    function showAdventure(id) {
        var data = "model=Adventures" + "&method=getAdventure" + "&id=" + id;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                data = response[1].split("|");
                console.log(data);
                var div = document.getElementById("show_adventure");
                var tr = div.getElementsByTagName("TR")[1];
                var td = tr.children;
                for(var i = 0; i < data.length; i++) {
                    td[i].innerHTML = data[i];
                }
                div.style = "display: inline";
            }
        });
    }
    function joinAdventure(id) {
        var data = "model=AdventureRequest" + "&method=joinAdventure" + "&id=" + id;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {                
                inputHandler.fetchBuilding("adventures");
            }
        });
    }
    var timer;
    function chk_me(){
        clearTimeout(timer);
        timer = setTimeout(checkUser, 1000);
    }
    function checkUser() {
        var div = document.getElementById("invite");
        var input = div.querySelectorAll("input")[0].value;
        var field = div.querySelectorAll("span")[0];
        var data = "model=Adventures" + "&method=checkUser" + "&username=" + input;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                if(response[1] == "") {
                    field.innerHTML = "User doesn't exists!";
                }
                else {
                    field.innerHTML = jsUcfirst(input) + " " + "is a" + " " + response[1];
                }
            }
        }); 
    }
    function adventureRequest(id, route) {
        var name = false;
        if(route == 'invite') {
            name = document.getElementById("invite").children[0].value;
        }
        var data = "model=AdventureRequest" + "&method=request" + "&id=" + id + "&route=" + route + "&invitee=" + name;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                // reset form
            }       
        });
    }
    function provide() {
        let item = false;
        let quantity = false;
        let warrior_check = false;
        console.log(document.getElementsByClassName("warriors"));
        if(document.getElementsByClassName("warriors").length === 0) {
            console.log('hello');
            if(document.getElementById("selected").children.length == 0) {
                gameLogger.addMessage("Please select a item");
                gameLogger.logMessages();
                return false;
            }
            item = document.getElementById("selected").querySelectorAll("figcaption")[0].innerHTML.toLowerCase();
            quantity = document.getElementById("selected_amount").value;
            if(quantity == 0) {
                gameLogger.addMessage("Please select a valid amount");
                gameLogger.logMessages();
                return false;
            }
        }
        else {
            warrior_check = warriorsCheck();
            if(warrior_check.length == 0) {
                gameLogger.addMessage("Please select warriors");
                gameLogger.logMessages();
                return false;
            }
        }
        let data = "model=SetAdventure" + "&method=provide" + "&item=" + item + "&quantity=" + quantity 
        + "&warrior_check=" + warrior_check;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                var responseText = response[1];
                document.getElementById("requirements").getElementsByTagName("tbody")[0].innerHTML = responseText.html[0];
                if(typeof(warrior_check) !== 'undefined') {
                    document.getElementById("provide").innerHTML = responseText.html[1];
                }
                else {
                    document.getElementById("selected").innerHTML = "";
                    updateInventory('adventures');
                    document.getElementById("provide").querySelectorAll("input")[0].value = "";
                }
            }       
        });
    }
    function updateInfo(info) {
        var table = document.getElementById("requirements").children[0];
        var number;
        switch(info[0]) {
            case 'farmer':
                number = 0;
                break;
            case 'miner':
                number = 1;
                break;
            case 'warrior':
                number = 2;
                break;
        }
        console.log(table.children[1].children[number].children[3]);
        table.children[1].children[number].children[3].innerHTML = info[1];
        table.children[1].children[number].children[4].innerHTML = info[2];
        if(status == 1) {
            table.children[1].children[number].children[1].innerHTML = 1;
        }
    }
    function updateAdventure() {
        var data = "model=UpdateAdventure" + "&method=updateAdventure";
        ajaxP(data, function (response) {
            console.log(response);
            if(response[0] !== false) {
                let responseText = response[1];
                openNews(responseText.html, true);
                // Add events to item elements
                itemTitle.addItemClassEvents();
                document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function() {
                    inputHandler.fetchBuilding('adventures');
                });
                updateInventory();
            }
        });
    }
    function leaveAdventure() {
        var data = "model=Adventures" + "&method=leaveAdventure";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                inputHandler.fetchBuilding('adventures'); 
            }
        });
    }
    