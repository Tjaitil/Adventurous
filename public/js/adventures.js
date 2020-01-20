    var provide_div = document.getElementById("provide");
    if(provide_div != null && provide_div.children.length > 0) {
        document.getElementById("provide").getElementsByTagName("button")[0].addEventListener('click', provide);
    }
    if(document.getElementById("time") != null) {
        document.getElementById("time").addEventListener("load", getCountdown());
    }
    window.onload = function () {
        if(document.getElementById("invite") != null) {
            var buttons = document.getElementById("invite").querySelectorAll("button");
            for(var i = 0; i < buttons.length; i++) {
                if(buttons[i].innerText.indexOf("Toggle") != -1) {
                    buttons[i].addEventListener("click", toggleInvite);
                    break;
                }
            }
        }
        // Add event to the warriors
        var divs = document.querySelectorAll(".warriors");
        if(divs.length > 0) {
            divs.forEach(function(element) {
                // ... code code code for this one element
                element.addEventListener('click', function() {
                    check();
                });
            });
        }
        // If div with id people does not exists there is no active adventure
        if(document.getElementById("people") != null) {
            document.getElementsByName("leave_adventure")[0].addEventListener("click", leaveAdventure);
        }
    };
    function getCountdown() {
        var data = "model=adventures" + "&method=getCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var fetch = data[1];
                console.log(fetch);
                console.log(data);
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0 && fetch === "1"){
                        clearInterval(x);
                        /*document.getElementById("current_adventure").removeChild(document.getElementById("adv_start"));*/
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Fetch report");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateAdventure);
                        document.getElementById("report").appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
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
    function startAdventure() {
        var data = "model=AdventureStatus" + "&method=startAdventure";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                gameLog(response[1]);
                document.getElementById("status").children[0].innerHTML = "Adventure status: underway!";
                getCountdown();
            }
        });
    }  
    function show(element) {
        id = element.id;
        switch(element) {
            case 'pending_adventure':
                document.getElementById("pending_adventure").style = "display: inline;";
                document.getElementById("current_adventure").style = "display: none;";
                document.getElementById("new_adventure").style = "display: none;";
                break;
            case 'current_adventure':
                document.getElementById("pending_adventure").style = "display: none;"; 
                document.getElementById("current_adventure").style = "display: inline;";
                document.getElementById("new_adventure").style = "display: none;";
                break;
            case 'new_adventure':
                document.getElementById("pending_adventure").style = "display: none;";
                document.getElementById("current_adventure").style = "display: none;";
                document.getElementById("new_adventure").style = "display: inline;";
                break;
        }
    }
    function showAdventure(id) {
        var data = "model=adventures" + "&method=getAdventure" + "&id=" + id;
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
            if(response[0] != false) {
                gameLog(response[1]);
                window.location.replace("/adventures");
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
        var data = "model=adventures" + "&method=checkUser" + "&username=" + input;
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
                gameLog(response[1]);
            }       
        });
    }
    function provide() {
        var data;
        var item;
        if(document.getElementsByClassName("warriors").length == 0) {
            if(document.getElementById("selected").children.length == 0) {
                gameLog("Please select a item");
                return false;
            }
            item = document.getElementById("selected").children[0].children[1].innerHTML;
            var quantity = document.getElementById("quantity").value;
            if(quantity == 0) {
                gameLog("Please select a valid amount");
                return false;
            }
            input.value = 0;
            data = "model=setadventure" + "&method=provide" + "&item=" + item + "&quantity=" + quantity;
        }
        else {
            var warrior_check = warriorsCheck();
            if(warrior_check.length == 0) {
                gameLog("Please select warriors");
                return false;
            }
            data = "model=setadventure" + "&method=provide" + "&warrior_check=" + warrior_check;    
        }
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                var responseText = response[1].split("|");
                gameLog(responseText[0]);
                document.getElementById("requirements").getElementsByTagName("tbody")[0].innerHTML = responseText[1];
                if(typeof warrior_check !== undefined) {
                    document.getElementById("provide").innerHTML = responseText[2];
                }
                else {
                    document.getElementById("selected").innerHTML = "";
                    updateInventory('adventures');
                }
            }       
        });
    }
    function check(checkbox) {
        var parent = checkbox.parentNode;
        if(checkbox.checked) {
            document.getElementById(parent.id).style = "border: 1px solid red";
        }
        else {
            document.getElementById(parent.id).style = "border: 1px solid black";
        }
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
        console.log("update_adventure");
        var data = "model=UpdateAdventure" + "&method=updateAdventure";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                openNews(response[1]);
                getCountdown();
            }
        });
    }
    function leaveAdventure() {
        var data = "model=adventures" + "&method=leaveAdventure";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
                window.location.replace("/adventures");
            }
        });
    }
    