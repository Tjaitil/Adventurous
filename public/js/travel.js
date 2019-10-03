    /* function capitalizeFirstLetter(string) {
        return string[0].toUpperCase() + string.slice(1);
    }
    function createLinks (name, top, right, bottom, left) {
        this.name = name;
        this.top = top;
        this.right = right;
        this.bottom = bottom;
        this.left = left;
        var buttonName = capitalizeFirstLetter(this.name);
        var button = document.createElement("BUTTON");
        var buttonText = document.createTextNode(buttonName);
        button.appendChild(buttonText);
        document.getElementById("canvas_area").appendChild(button);
        button.onclick = travel(this.name);
        
        
        /*link.setAttribute("href", '#');
        link.onclick = function hello () {alert('Hello');}; return false;
        link.onclick = travel.apply(this.href, arguments);
        link.id= "city_" + this.href + "_link";
        document.getElementById("canvas_area").appendChild(link);
        document.getElementById("city_" + this.href + "_link").style.top = this.top + "px";
        document.getElementById("city_" + this.href + "_link").style.right = this.right + "px";
        document.getElementById("city_" + this.href + "_link").style.bottom = this.bottom + "px";
        document.getElementById("city_" + this.href + "_link").style.left = this.left + "px"; 
    }
    
    createLinks('towhar', 30, 25, 170, 0);
    createLinks('golbak', 30, 40, 150, 0);
    createLinks('snerpiir', 30, 30, 120, 0);
    createLinks('krasnur', 30, 30, 100, 0);
    createLinks('tasnobil', 30, 0, 80, 0);
    createLinks('cruendo', 30, 110, 60, 0);
    createLinks('fagna', 30, 0, 30, 40);  */
    window.addEventListener("load", countdown());
    function countdown() {
        document.getElementById("travel").innerHTML = "Travelling done";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                result = this.responseText.split("|");
                var time = result[0] * 1000;
                var info = "Currently travelling to:" + " ";
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("travel_time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    document.getElementById("travel").innerHTML = info + result[1];
                    if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("travel").innerHTML = "";
                        document.getElementById("travel_time").innerHTML = "Travelling done";
                        updateLocation(result[1]);
                    }
                }, 1000);
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=travel" + "&method=checkCountdown");
        ajaxRequest.send();
    }
    
    function travel(destination) {
        var data = "model=travel" + "&method=getData" + "&destination=" + destination;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.length > 0) {
                    gameLog(this.responseText);
                }
                else {
                    countdown();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    
    function updateLocation(destination) {
        var data = "model=travel" + "&method=updateLocation" + "&destination=" + destination;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.respoonseText);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    