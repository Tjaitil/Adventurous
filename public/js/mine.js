    
    window.addEventListener("load", function () {
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
        document.getElementById("mine_form").children[12].addEventListener("click", setMine);
    });
    
    var intervals = [];
    function getCountdown() {
        document.getElementById("mining").innerHTML = "Currently Mining";
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                var data = this.responseText.split("|");
                var time = data[0] * 1000;
                var fetch = data[1];
                console.log(data);
                var x = setInterval (function() {
                    intervals.push(x);
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
                        document.getElementById("time").innerHTML = "";
                        document.getElementById("time").appendChild(btn);
                        document.getElementById("mining").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("mining").innerHTML = "No miners at work";
                        document.getElementById("time").innerHTML = "";
                    }
                }, 1000);
            }
        };
        ajaxRequest.open("GET", "/handlers/handler_g.php?&model=mine" + "&method=checkCountdown");
        ajaxRequest.send();
    }
    function showMineral() {
        var element = event.target;
        var clone = element.cloneNode(true);
        clone.removeAttribute("onclick");
        var mineral = element.getAttribute("alt");
        var div = document.getElementById("mine_form");
        div.style.visibility = "visible";
        div.children[1].value = jsUcfirst(mineral);
        div.children[4].value = this[mineral].time;
        div.children[7].value = this[mineral].permits;
        if(div.children[0].tagName != 'IMG') {
            div.insertBefore(clone, div.children[0]);
        }
        else {
            clone.src = this[mineral].src;
            console.log(this[mineral].src);
        }
    }
    function setMine() {
        var form = document.getElementById("mine_form");
        
        if(!form[3].reportValidity()) {
            console.log("error");
        }
        else {
            var mineral = form[0].value;
            var workforce = form[3].value;
            var data = "model=SetMine" + "&method=setMine" + "&mineral=" + mineral + "&workforce=" + workforce;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    getCountdown();
                    // rT = repsonseText from AJAX request
                    var rT = response[1].split("|");
                    var form = document.getElementById("mine_form");
                    form.children[11].innerHTML = "(" + rT[1] + ")";
                    form.children[0].innerHTML = "Total permits:" + rT[0];
                }
            });
        }
        console.log(intervals);
    }
    function updateMine() {
        var data = "model=UpdateMine" + "&method=updateMine";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                getCountdown();
                // rT = responseText
                var rT = response[1].split("|");
                gameLog(rT[0]);
                show_xp('miner', rT[1]);
            }       
        });
        console.log(intervals);
    }
    function cancelMining() {
        var data = "model=mine" + "&method=cancelMining";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                window.clearInterval(intervals.pop());
                getCountdown();
                gameLog(response[1]);
            }       
        });
        console.log(intervals);
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
        this.src = "public/img/" + name + " ore.png";
        this.permits = permits;
        this.time = time;
    }
    var iron =  new newMineral('iron', null, null);
    var adron =  new newMineral('adron', 10, 200);