    function getCountdown() {   
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
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
                        var t = document.createTextNode("Fetch Minerals");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateMine);
                        document.getElementById('growing').appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "No miners at work";
                    }
                }, 1000);
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_g.php?&model=miningarea" + "&method=checkCountdown");
        ajaxRequest.send();
    }

    window.onload = getCountdown();
    
    function updateMine() {
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    getCountdown();
                }
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_js.php?&model=updatemine" + "&method=updateMine");
        ajaxRequest.send();
    }
    function img() {
        var img = document.getElementById("type_img");
        var select = document.getElementById("form_select");
        var name = select.children[select.selectedIndex].value;
        if(name.length < 1) {
            return;
        }
        img.style = "display:block";
        img.src = "public/images/" + name;
    }