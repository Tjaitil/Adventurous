    
    function getCountdown() {
        var data = "model=armymissions" + "&method=getCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                var data = response[1].split("|");
                var time = data[0] * 1000;
                var mission = data[1];
                console.log(data);
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
                    }
                    else if (distance < 0) {
                        clearInterval(x);
                        document.getElementById("time").innerHTML = "None";
                    }
                }, 1000);
            }
        });
    }
    window.onload = getCountdown();
    var div = document.getElementById("mission_enabled").innerHTML;
    
    function prepareMission(tr_id, mission_id) {
        var tr = tr_id.parentNode.parentNode;
        var clone = tr.cloneNode(true);
        clone.deleteCell(5);
        clone.setAttribute("id", mission_id);
        console.log(clone.id);
        var ele = document.getElementById("mission_enabled");
        ele.style.visibility = "visible";
        var checkbox;
        document.getElementById("mission_table").appendChild(clone);
        var data = "model=armymissions" + "&method=getWarriors";
        ajaxJS(data, function(response) {
            console.log(response[1]);
            if(response[0] != false) {
                /*var warriors = response[1].split("||");
                warriors.pop();
                for(var i = 0; i < warriors.length; i++) {
                    var warrior = warriors[i].split("|");
                    var div = document.createElement("DIV");
                    div.setAttribute("class", "warriors");
                    div.setAttribute("id", "warrior_" + [i + 1]);
                        for(var x = 0; x < warrior.length; x++) {
                            div.innerHTML += warrior[x];
                        }
                    checkbox = document.createElement("input");
                    checkbox.setAttribute("type", "checkbox");
                    
                    div.appendChild(checkbox);
                    document.getElementById("warriors").appendChild(div);
                    //Call function and provide it with checkbox. It will return a function which then calls another function.
                    //Doing it this way you can add a event with a parameter.
                    checkbox.addEventListener("click", checkboxed(checkbox))
                    checkbox.addEventListener("click", check);
                }*/
                document.getElementById("warriors_container").innerHTML = response[1];
                var divs = document.querySelectorAll(".warriors");
                divs.forEach(function(element) {
                    // ... code code code for this one element
                    element.addEventListener('click', function() {
                        check();
                    });
                });
            }
        });
    }
    function checkboxed(checkbox) {
        return function(){
            check();
        };
    }
    function check() {
        var div = event.target.closest(".warriors");
        var checkbox = div.querySelectorAll("input[type=checkbox]")[0];
        if(checkbox.checked) {
            document.getElementById(div.id).style = "border: 1px solid red";
        }
        else {
            document.getElementById(div.id).style = "border: 1px solid black";
        }
    }
    function exit() {
        var ele = document.getElementById("mission_enabled");
        ele.style.visibility = "hidden";
        ele.innerHTML = div;
    }
    function doMission() {
        var warriors_div = document.getElementsByClassName("warriors");
        console.log(warriors_div[1]);
        var warrior_check = [];
        for(var i = 0; i < warriors_div.length; i++) {
            var checkbox = warriors_div[i].querySelectorAll("input[type=checkbox]")[0];
            if(checkbox.checked === true) {
                var warrior = warriors_div[i].id;
                var warror_id = warrior.replace("warrior_", "");
                warrior_check.push(warror_id);
            }
        }
        // Send array with warriors id and mission id to model
        console.log(warrior_check);
        var mission_id = document.getElementById("mission_table").children[0].id;
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
    function updateMission() {
        var data = "model=UpdateArmymission" + "&model=updateMission";
        ajaxP(data, function (response) {
            if(response[0] !== false) {
                document.getElementById('current_mission').innerHTML = "";
                document.getElementById("time").innerHTML = "None";
            }
        });
    }