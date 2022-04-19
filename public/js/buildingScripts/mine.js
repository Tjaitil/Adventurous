    if(document.getElementById("news_content").children[2] != null) {
        getCountdown();
        var img = document.getElementById("select").querySelectorAll("img");
        img.forEach(function(element) {
              // ... code code code for this one element
                element.addEventListener('click', showSelect);
            });
        document.getElementById("cancel_action").addEventListener("click", cancelMining);
        /*document.getElementById("mineral_select").getElementsByTagName("img").addEventListener("click", showMineral);*/
        document.getElementById("data_container").querySelectorAll("button")[0].addEventListener("click", setMine);
        fetchData();
    }
    var intervals = [];
    function getCountdown() {
        document.getElementById("mining").innerHTML = "No miners at work";
        var data = "&model=Mine" + "&method=checkCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                let time = responseText.data * 1000;
                let fetch = responseText.fetch_minerals;
                if(responseText.mining_type !== 'none') {
                    document.getElementById("mining").innerHTML = "Currently mining " + responseText.mining_type;    
                }
                let x = setInterval (function() {
                    intervals.push(x);
                    let now = new Date().getTime();
                    let distance = time - now;
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("cancel_action").style.visibility = "";
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
                    }
                    // Check if countdown is finished and fetch is true
                    if (distance < 0 && fetch === "1"){
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Fetch Minerals");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateMine);
                        document.getElementById("cancel_action").style.visibility = "hidden";
                        document.getElementById("time").innerHTML = "";
                        document.getElementById("time").appendChild(btn);
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
    function setMine() {
        let mineral = document.getElementsByName("mineral")[0].value;
        let workforceInput = document.getElementsByName("workforce")[0];
        let workforce = workforceInput.value;
        if(workforce === 0) { 
            gameLogger.addMessage("ERROR You need to select at least 1 worker");
            gameLogger.logMessages();
        }
        workforceInput.value = "";
        let data = "model=SetMine" + "&method=setMine" + "&mineral=" + mineral + "&workforce=" + workforce;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1];
                updateHunger(responseText.newHunger);
                getCountdown();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = 
                '(' + responseText.availWorkforce + ')';
                document.getElementById("data_container").querySelectorAll("p")[0].innerHTML = 
                    "Total permits:" + responseText.permits;
            }
        });
    }
    function updateMine() {
        if(checkInventoryStatus()) {
            gameLogger.addMessage(commonMessages.inventoryFull, true);
            return false;
        }
        let data = "model=UpdateMine" + "&method=updateMine";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                let responseText = response[1];
                getCountdown();
                updateInventory();
                updateCountdownTab();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
            }       
        });
    }
    function cancelMining() {
        var data = "model=Mine" + "&method=cancelMining";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                // Clear interval started by getCountdown
                let responseText = response[1];
                window.clearInterval(intervals.pop());
                updateCountdownTab();
                getCountdown();
                document.getElementById("data_container_avail_workforce").innerText = '(' + responseText.availWorkforce + ')';
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
        this.src = "public/images/" + name + " ore.png";
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
