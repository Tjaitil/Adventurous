    function clock() {
        var today = new Date();
        var hours = today.getHours();
        var minutes = today.getMinutes();
        var seconds = today.getSeconds();
        minutes = checkZero(minutes);
        seconds = checkZero(seconds);
        document.getElementById("clock").innerHTML =
        hours + ":" + minutes + ":" + seconds;
        var t = setTimeout(clock, 1000);
    }
    function checkZero(i) {
        if (i < 10) {i = "0" + i;}
        return i;
    }
    window.onload = clock();
    
    function displayNav() {
        document.getElementById("nav_2").style = "display: block";
    }
    
    function gameLog(message) {
        if(message.search("\\[") == -1) {
            var d = new Date();
            var time = "[" + d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds() + "] ";
            message = time + message;
        }
        var tr = document.createElement("TR");
        var td = document.createElement("TD");
        var table = document.getElementById("game_messages");
        tr.appendChild(td);
        td.innerHTML = message;
        table.appendChild(tr);
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
    
    function closeNews() {
        console.log('hello');
        var news = document.getElementById("news");
        news.innerHTML = "";
        news.style = "visibility: hidden;";
    }
    
    function get_xp(skill, element) {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                data.shift();
                var skillName = skill.charAt(0).toUpperCase() + skill.slice(1);
                element.title = skillName + '\n' + "Current xp: " + data[0] + '\n' + "Next level: " + data[1];
            }
        };
        ajaxRequest.open('GET', "handlers/handler_ses.php?variable=" + skill);
        ajaxRequest.send();
    }
    
    function close_xp(skill) {
        
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
                console.log(this.responseText);
                document.getElementById("inventory").innerHTML = this.responseText;
            }
        };
        ajaxRequest.open('GET', "handlers/handlerf.php?file=inventory" + "&page=" + page);
        ajaxRequest.send();
    }