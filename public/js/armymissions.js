    if(document.getElementById("news_content").children[2] != null) {
        getCountdown();
        document.getElementById("current_mission").querySelectorAll("button")[0].addEventListener("click", cancelMission);
        document.getElementById("news_content_main_content").querySelectorAll("button")[0].addEventListener("click", function() {
           game.fetchBuilding('armycamp'); 
        });
    }
    function getCountdown() {
        var data = "model=ArmyMissions" + "&method=getCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var mission = data[1];
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    if(document.getElementById("time") == null) {
                        clearInterval(x);
                    }
                    else {
                        document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    }
                    if(distance < 0 && mission != 0){
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Mission report");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateMission);
                        document.getElementById('current_mission').appendChild(btn);
                        document.getElementById("current_mission").querySelectorAll("button")[0].display = "none";
                        document.getElementById("time").innerHTML = "Finished";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "None";
                        document.getElementById("current_mission").querySelectorAll("button")[0].display = "none";
                    }
                }, 1000);
            }
        });
    }
    function prepareMission(tr_id, mission_id) {
        var tr = tr_id.parentNode.parentNode;
        var clone = tr.cloneNode(true);
        clone.deleteCell(5);
        clone.setAttribute("id", mission_id);
        var ele = document.getElementById("mission_enabled");
        console.log(document.getElementById("mission_table").firstChild);
        /* Check wether mission table is visible, if so replace node instead of appending to prevent two rows with missions
           Plus if the mission table there is no need to make ajax request to get warrior data */
        if(document.getElementById("mission_table").firstChild && ele.style.visibility == "visible") {
            document.getElementById("mission_table").replaceChild(clone, document.getElementById("mission_table").children[0]);
        }
        else {
            document.getElementById("mission_table").appendChild(clone);
            ele.style.visibility = "visible";
            var data = "model=ArmyMissions" + "&method=getWarriors";
            ajaxJS(data, function(response) {
                console.log(response[1]);
                if(response[0] != false) {
                    document.getElementById("mission_enabled").innerHTML += response[1];
                    var divs = document.querySelectorAll(".warriors");
                    divs.forEach(function(element) {
                        element.addEventListener('click', function() {
                            check();
                        });
                        element.querySelectorAll("button")[0].addEventListener('click', flipWarriorCard);
                    });
                }
            });
        }
    }
    function exit() {
        var ele = document.getElementById("mission_enabled");
        ele.style.visibility = "hidden";
        ele.innerHTML = div;
        document.getElementById("mission_table").innerHTML = "";
        // Loop through children and find warriors_container and remove it
        for(var i = 0; i < ele.children.length; i++) {
            if(ele.children[i].id == "warriors_container") {
                ele.removeChild(ele.children[i]);
                break;
            }
        }
    }
    function doMission() {
        // warriorSelect.js
        var warrior_check = warriorsCheck();
        console.log(warrior_check);
        // Send array with warriors id and mission id to model
        if(warrior_check.length == 0) {
            gameLog("Please select warriors");
            return false;
        }
        var mission_id = document.getElementById("mission_table").querySelectorAll("tr")[0].id;
        console.log(document.getElementById("mission_table").querySelectorAll("tr")[0]);
        var data = "model=SetArmymission" + "&method=setMission" + "&mission_id=" + mission_id + "&warrior_check=" + warrior_check;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] != false) {
                getCountdown();
                updateCountdownTab();
                gameLog(response[1]);
                document.getElementById("mission_table").innerHTML = "";
                document.getElementById("warriors_container").innerHTML = "";
                document.getElementById("mission_enabled").style.visibility = "hidden";
                newLevel.searchString(response[1]);
            }
        });
    }
    function cancelMission() {
        var data = "model=UpdateArmymission" + "&method=cancelMission";
        ajaxP(data, function(response) {
            if(response[0] != false) {
                getCountdown();
                updateCountdownTab();
                gameLog(response[1]);
            }
        });
    }
    function updateMission() {
        var data = "model=UpdateArmymission" + "&method=updateMission";
        ajaxP(data, function (response) {
            console.log(response);
            if(response[0] !== false) {
                let responseText = response[1].split("|");
                updateCountdownTab();
                updateInventory();
                gameLog(responseText[1]);
                document.getElementById("current_mission").querySelectorAll("button")[1].display = "none";
                document.getElementById("time").innerHTML = "None";
                // Search string for next level message
                newLevel.searchString(response[1]);
            }
        });
    }