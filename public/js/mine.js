    if(document.getElementById("news_content").children[2] != null) {
        getCountdown();
        var img = document.getElementById("select").querySelectorAll("img");
        img.forEach(function(element) {
              // ... code code code for this one element
                element.addEventListener('click', showSelect);
            });
        document.getElementById("cancel").addEventListener("click", cancelMining);
        /*document.getElementById("mineral_select").getElementsByTagName("img").addEventListener("click", showMineral);*/
        document.getElementById("data_form").querySelectorAll("button")[0].addEventListener("click", setMine);
    }
    var intervals = [];
    function getCountdown() {
        document.getElementById("mining").innerHTML = "No miners at work";
        var data = "&model=mine" + "&method=checkCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var fetch = data[1];
                if(data[2] !== 'none') {
                    document.getElementById("mining").innerHTML = "Currently mining " + jsUcfirst(data[2]);    
                }
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
        });
    }
    /*function showMineral() {
        var element = event.target;
        var clone = element.cloneNode(true);
        clone.removeAttribute("onclick");
        var mineral = element.getAttribute("alt");
        var div = document.getElementById("mine_form");
        div.style.visibility = "visible";
        console.log(div.children[1]);
        var div_inputs = div.querySelectorAll("input");
        div_inputs[0].value = jsUcfirst(mineral);
        div_inputs[1].value = this[mineral].time;
        div_inputs[2].value = this[mineral].permits;
        if(div.children[0].children.length == 0) {
            div.children[0].appendChild(clone);
        }
        else {
            clone.src = this[mineral].src;
        }
    }*/
    function setMine() {
        var form = document.getElementById("data_form");
        
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
                    // RepsonseText from AJAX request
                    var responseText = response[1].split("|");
                    document.getElementById("data_form").querySelectorAll("span")[0].innerHTML = "(" + responseText[1] + ")";
                    document.getElementById("data_container").children[0].innerHTML = "Total permits:" + responseText[0];
                    document.getElementById("data_form").style.visibility = "hidden";
                    newLevel.searchString(response[1]);
                }
            });
        }
        console.log(intervals);
    }
    function updateMine() {
        var data = "model=UpdateMine" + "&method=updateMine";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                getCountdown();
                var responseText = response[1].split("|");
                gameLog(responseText[0]);
                if(responseText[1].indexOf("[") != -1) {
                    gameLog(responseText[1]);
                    show_xp('miner', responseText[1]);
                    document.getElementById("data_form").querySelectorAll("span")[0].innerHTML = "(" + responseText[2] + ")";
                }
                else {
                    show_xp('miner', responseText[1]);
                    document.getElementById("data_form").querySelectorAll("span")[0].innerHTML = "(" + responseText[2] + ")";   
                }
                show_xp('miner', responseText[1]);
                updateInventory("mine");
            }       
        });
        console.log(intervals);
    }
    function cancelMining() {
        var data = "model=Mine" + "&method=cancelMining";
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
    var typeData = {
        iron: new newMineral('iron', null, null),
        steel: new newMineral('steel', null, null),
        clay: new newMineral('clay', 10, 200),
        gargonite: new newMineral('gargonite', 10, 200),
        adron: new newMineral('adron', 10, 200),
        yeqdon: new newMineral('yeqdon', null, null),
        frajrite: new newMineral('frajrite', 10, 200)
    };
