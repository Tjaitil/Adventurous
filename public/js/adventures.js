    var div = document.getElementById("time");
    console.log(div);
    
    if(div != null) {
        div.addEventListener("load", getCountdown());
    }
    
    function getCountdown() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                var time = data[0] * 1000;
                var fetch = data[1];
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
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=adventures" + "&method=getCountdown");
        ajaxRequest.send();
    }
    
    var figures = document.getElementsByTagName("FIGURE");
    for(var i = 0; i < figures.length; i++) {
        figures[i].addEventListener('click', handle);
    }
    
    function handle() {
        console.log(this);
    }
    
    
    function startAdventure() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=adventurestatus" + "&method=startAdventure");
        ajaxRequest.send();
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
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                data = this.responseText.split("|");
                console.log(data);
                var div = document.getElementById("show_adventure");
                var tr = div.getElementsByTagName("TR")[1];
                var td = tr.children;
                for(var i = 0; i < data.length; i++) {
                    td[i].innerHTML = data[i];
                }
                div.style = "display: inline";
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=adventures" + "&method=getAdventure" + "&id=" + id);
        ajaxRequest.send();
    }
    
    function joinAdventure(id) {
        var data = "model=adventurerequest" + "&method=joinAdventure" + "&id=" + id;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    var timer;
    
    function chk_me(){
        clearTimeout(timer);
        timer=setTimeout(checkUser(),1000);
    }
    
    function checkUser() {
        var div = document.getElementById("invite");
        var input = div.children[0].value;
        var field = div.children[1];
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText == "") {
                    field.innerHTML = "User doesn't exists!";
                }
                else {
                    field.innerHTML = input + " " + "is a" + " " + this.responseText;
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_g.php?model=adventures" + "&method=checkUser" + "&username=" + input);
        ajaxRequest.send();
    }
    
    function adventureRequest(id, route) {
        var name;
        if(route == 'invite') {
            name = document.getElementById("invite").children[0].value;
        }
        var data = "model=adventurerequest" + "&method=adventureRequest" + "&id=" + id + "&route=" + route + "&invitee=" + name;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    
    function provide(id, route) {
        var data;
        if(route == 'item') {
            var item = document.getElementById("selected").children[0].children[1].innerHTML;
            console.log(item);
            var input = document.getElementById("quantity");
            var quantity = input.value;
            input.value = 0;
            data = "model=setadventure" + "&method=provide" + "&id=" + id +
                            "&item=" + item + "&quantity=" + quantity + "&route=" + route;
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    if(this.responseText.search("ERROR") != false) {
                        treatData(this.responseText);
                    }
                }
            };
            ajaxRequest.open('POST', "handlers/handler_p.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
        }
        else if(route == 'warrior') {
            var warriors_div = document.getElementsByClassName("warriors");
            var warrior_check = [];
            for(var i = 0; i < warriors_div.length; i++) {
                var checkbox = warriors_div[i].children[1];
                if(checkbox.checked === true) {
                    var warrior = warriors_div[i].id;
                    var warror_id = warrior.replace("warrior_", "");
                    warrior_check.push(warror_id);
                }
            }
            console.log(warrior_check);
            data = "model=setadventure" + "&method=provide" + "&id=" + id +
                             "&warrior_check=" + warrior_check + "&route=" + route;
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    if(this.responseText.search("ERROR") != false) {
                        treatData(this.responseText);
                    }
                }
            };
            ajaxRequest.open('POST', "handlers/handler_p.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
        }
        function treatData(variabel) {
            var data = variabel.split("#");
            var info = data[0].split("|").slice(1);
            // Alert or do something with the first message.
            updateInfo(info);
            updateinventory(data[1]);
        }
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
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=UpdateAdventure" + "&method=updateAdventure");
        ajaxRequest.send();
    }
    