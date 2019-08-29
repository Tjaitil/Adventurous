    window.onload = function () {
        getCountdown();
        var img = document.getElementById("mineral_select").querySelectorAll("img");
        img.forEach(function(element) {
              // ... code code code for this one element
                element.addEventListener('click', function() {
                    showMineral();
                });
            });
        document.getElementById("cancel").addEventListener("click", cancelMining);
        /*document.getElementById("mineral_select").getElementsByTagName("img").addEventListener("click", showMineral);*/
    };
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
                        document.getElementById('mining').appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "No miners at work";
                    }
                }, 1000);
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_g.php?&model=mine" + "&method=checkCountdown");
        ajaxRequest.send();
    }
    function showMineral() {
        var element = event.target;
        var clone = element.cloneNode();
        clone.removeAttribute("onclick");
        var mineral = element.getAttribute("alt");
        var div = document.getElementById("mineral_data");
        div.style.visibility = "visible";
        div.children[0].value = jsUcfirst(mineral);
        div.children[1].value = mineral.permits;
        div.children[2].value = mineral.time;
        div.insertBefore(div.children[0], clone);
    }
    function updateMine() {
        var data = "model=UpdateMine" + "&method=updateMine";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                getCountdown();
            }       
        });
    }
    function cancelMining() {
        var data = "model=mine" + "&method=cancelMining";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                getCountdown();
                gameLog(response[1]);
            }       
        });
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
    function newMineral(name, permits, time) {
        this.src = "public/img/" + name + " ore.jpg";
        this.permits = permits;
        this.time = time;
    }
    var iron = newMineral(iron, null, null);