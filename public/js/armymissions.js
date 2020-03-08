    window.addEventListener("load", function () {
      getCountdown();
      document.getElementById("current_mission").querySelectorAll("button")[0].addEventListener("click", cancelMission);
    });
    
    function getCountdown() {
        var data = "model=ArmyMissions" + "&method=getCountdown";
        ajaxG(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var mission = data[1];
                console.log(data);
                var canc_button = document.getElementById("current_mission").querySelectorAll("button")[0];
                var x = setInterval (function() {
                    var now = new Date().getTime();
                    var distance = time - now;
                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                    document.getElementById("time").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
                    if (distance < 0 && mission != 0){
                        clearInterval(x);
                        var btn = document.createElement("BUTTON");
                        var t = document.createTextNode("Mission report");
                        btn.appendChild(t);
                        btn.addEventListener("click", updateMission);
                        document.getElementById('current_mission').appendChild(btn);
                        document.getElementById("time").innerHTML = "Finished";
                        canc_button.style.display = "none";
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "None";
                        canc_button.style.display = "none";
                    }
                    else {
                        canc_button.style.display = "block";
                    }
                }, 1000);
            }
        });
    }
    
    var div = document.getElementById("mission_enabled").innerHTML;
    
    function prepareMission(tr_id, mission_id) {
        var tr = tr_id.parentNode.parentNode;
        var clone = tr.cloneNode(true);
        clone.deleteCell(5);
        clone.setAttribute("id", mission_id);
        console.log(clone.id);
        var ele = document.getElementById("mission_enabled");
        ele.style.visibility = "visible";
        document.getElementById("mission_table").appendChild(clone);
        var data = "model=ArmyMissions" + "&method=getWarriors";
        ajaxJS(data, function(response) {
            console.log(response[1]);
            if(response[0] != false) {
                document.getElementById("mission_enabled").innerHTML += response[1];
                var divs = document.querySelectorAll(".warriors");
                divs.forEach(function(element) {
                    // ... code code code for this one element
                    element.addEventListener('click', function() {
                        // warriorSelect.js
                        check();
                    });
                });
            }
        });
    }
    function exit() {
        var ele = document.getElementById("mission_enabled");
        ele.style.visibility = "hidden";
        ele.innerHTML = div;
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
            }
        });
    }
    function updateMission() {
        var data = "model=UpdateArmymission" + "&method=updateMission";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                document.getElementById('current_mission').innerHTML = "";
                document.getElementById("time").innerHTML = "None";
            }
        });
    }