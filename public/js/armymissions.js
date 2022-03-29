    if(document.getElementById("news_content").children[2] != null) {
        getCountdown();
        document.getElementById("current_mission").querySelectorAll("button")[0].addEventListener("click", cancelMission);
        document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function() {
            inputHandler.fetchBuilding('armycamp'); 
        });
        addWarriorEvents();
    }
    function getCountdown() {
        var data = "model=ArmyMissions" + "&method=getCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                var time = responseText.date * 1000;
                var mission = responseText.mission;
                var x = setInterval (function() {
                    let now = new Date().getTime();
                    let distance = time - now;
                    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    let seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("current_mission").querySelectorAll("button")[0].style.display = "";
                        document.getElementById("time").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
                    }
                    if(distance < 0 && mission != 0){
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Mission report");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateMission);
                        document.getElementById('current_mission').appendChild(btn);
                        document.getElementById("current_mission").querySelectorAll("button")[0].style.display = "none";
                        document.getElementById("time").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "None";
                        document.getElementById("current_mission").querySelectorAll("button")[0].style.display = "none";
                        if(document.getElementById("current_mission").querySelectorAll("button")[1] !== undefined) {
                            document.getElementById("current_mission").removeChild(document.getElementById("current_mission").querySelectorAll("button")[1]);
                        }
                    }
                }, 1000);
            }
        });
    }
    function prepareMission(tr_id, mission_id) {
        var tr = tr_id.parentNode.parentNode;
        let tds = [...tr.children];
        tds.pop();
        let infoContainers = document.getElementsByClassName("mission-info-container");
        tds.forEach((element, i) => infoContainers[i].children[1].innerHTML = element.innerHTML); 
        document.getElementById("mission_enabled").style.visibility = "visible";       
    }
    function exit() {
        let ele = document.getElementById("mission_enabled");
        ele.style.visibility = "hidden";
        document.getElementById("mission_table").innerHTML = "";
        // Loop through children and find warriors_container and remove it
        for(let i = 0; i < ele.children.length; i++) {
            if(ele.children[i].id === "warriors_container") {
                ele.removeChild(ele.children[i]);
                break;
            }
        }
    }
    function doMission() {
        // warriorSelect.js
        var warrior_check = warriorsCheck();
        // Send array with warriors id and mission id to model
        if(warrior_check.length == 0) {
            gameLogger.addMessage("Please select warriors!");
            gameLogger.logMessages();
            return false;
        }
        let mission_id = document.getElementById("mission_table").querySelectorAll("tr")[0].id;
        let data = "model=SetArmymission" + "&method=setMission" + "&mission_id=" + mission_id + "&warrior_check=" + warrior_check;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                getCountdown();
                updateCountdownTab();
                updateHunger(response.newHunger);
                document.getElementById("mission_table").innerHTML = "";
                document.getElementById("warriors_container").innerHTML = "";
                document.getElementById("mission_enabled").style.visibility = "hidden";
            }
        });
    }
    function cancelMission() {
        var data = "model=UpdateArmymission" + "&method=cancelMission";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                getCountdown();
                updateCountdownTab();
            }
        });
    }
    function updateMission() {
        var data = "model=UpdateArmymission" + "&method=updateMission";
        ajaxP(data, function (response) {
            console.log(response);
            if(response[0] !== false) {
                let responseText = response[1];
                let ele = document.getElementById("mission_enabled");
                for(let i = 0; i < ele.children.length; i++) {
                    if(["cont_exit", "battle_result"].indexOf(ele.children[i].className) === -1) {
                        ele.children[i].style.display = "none";
                    }
                }
                document.getElementById("battle_result").innerHTML = responseText[0];
                document.getElementById("battle_result").style.display = "";
                document.getElementById("mission_enabled").style.visibility = "visible";
                updateInventory();
                getCountdown();
            }
        });
    }