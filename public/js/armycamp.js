
    document.getElementById("actions").children[1].addEventListener("change", toogleActions);
    document.getElementById("actions").getElementsByTagName("button")[0].addEventListener("click", actions);
    window.addEventListener("load", function() {
        var warriors = document.getElementsByClassName("warrior");
        if(warriors.length > 0) {
            warriors = document.querySelectorAll(".warrior");
            warriors.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', function() {
                selectWarrior();
            });
        });
        }
        getCountdown();
    });
    function show(element) {
        var divs = ["overview", "calculator"];
        
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == element) {
                document.getElementById(divs[i]).style = "display: inline";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
            }
        }
    }
    function toggle(part, value = false) {
        if(part === 1) {
            var div = document.getElementById("calc_result");
            div.style = "display: none";
            div.innerHTML = "";
            document.getElementById("calc_form").style.removeProperty('display');
        }
        else if(part === 2) {
            if(value === 'individ') {
                document.getElementById("stats_individ").style = "display: block";
                document.getElementById("stats_group").style = "display: none";
                statsInput();
            }
            else if(value === 'group') {
                document.getElementById("stats_group").style.removeProperty('display');
                document.getElementById("stats_individ").style = "display: none";    
            }
        }
    }
    function statsInput() {
        var count = Number(document.getElementsByName("melee_amount")[0].value) +
                     Number(document.getElementsByName("ranged_amount")[0].value);
        var inputDiv = document.getElementById("stats_individ"); 
        var inputs = inputDiv.getElementsByTagName("INPUT").length;
        var newFields = count - inputs;
        if(newFields > 0) {
            for(var i = 0; i < newFields; i++) {
                var label = document.createElement("LABEL");
                label.appendChild(document.createTextNode("Warrior" + (inputs + i + 1) + ':'));
                label.setAttribute("for", "warrior" + (inputs + i + 1));
                var element = document.createElement("INPUT");
                element.setAttribute("type", "text");
                element.setAttribute("name", "warrior" + (inputs + i + 1));
                element.setAttribute("value", "1, 1, 1, 1, 10, 10");
                inputDiv.appendChild(label);
                inputDiv.appendChild(element);
                inputDiv.appendChild(document.createElement("BR"));
            }
        }
        else if(newFields < 0) {
            newFields = inputs - count;
            for(var x = 0; x < newFields; x++) {
                //Removes linebreak, input and label tag
                inputDiv.removeChild(inputDiv.lastElementChild);
                inputDiv.removeChild(inputDiv.lastElementChild);
                inputDiv.removeChild(inputDiv.lastElementChild);
            }
        }
    }
    function calculate() {
        /*var form = document.getElementById("calc_form").children[0].children;
        console.log(form);
        var form_data = {};
        for(var i = 0; i < form.length; i++) {
            if(form[i].tagName == 'DIV') {
                for(var x = 0; x < form[i].children.length; x++) {
                    console.log(form[i].children[x]);
                    if(form[i].children[x].tagName != 'INPUT') {
                        continue;
                    }
                    form_data[form[i].children[x].name] = form[i].children[x].value;
                }
            }
            else if(form[i].tagName != 'INPUT') {
                continue;
            }
            form_data[form[i].name] = form[i].value;
        }*/
        var form_data = {};
        form_data['warriors'] = ["1", "2", "3"];
        form_data['daqloon'] = 3;
        var data = "model=CombatCalculator" + "&method=calculate" + "&form_data=" + JSON.stringify(form_data) + "&route=" + "db";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                console.log(response[1]);
                show('calculator');
                document.getElementById("calc_form").style = "display: none";
                var div = document.getElementById("calc_result");
                div.style = "visibility: visible";
                div.innerHTML += response[1];
            }       
        }); 
    }
    function toogleActions() {
        var select = document.getElementsByName("action")[0];
        var value = select.options[select.selectedIndex].value;
        
        var divs = ["transfer", "heal", "training"];
        
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == value) {
                 document.getElementById(divs[i]).style.display = "block";
            }
            else if(document.getElementById(divs[i]) == null) {
                continue;
            }
            else {
                document.getElementById(divs[i]).style.display = "none";
            }
        }
    }
    function selectWarrior() {
        if(event.target.tagName === 'BUTTON') {
            return false;
        }
        var element = event.target.closest(".warrior");
        if(element.style.border === "3px solid black") {
            element.style.border = "1px solid black";
        }
        else {
            element.style.border = "3px solid black";
        }
        
    }
    function actions() {
        var select = document.getElementsByName("action")[0];
        var value = select.options[select.selectedIndex].value;
        var warriorsD = document.getElementsByClassName("warrior");
        // warrriorsI is the div index of the warrior;
        var warriorsI = [];
        // warriors is the id of warriors;
        var warriors = [];
        for(var i = 0; i < warriorsD.length; i++) {
            if(warriorsD[i].style.border === "3px solid black") {
                warriorsI.push(i);
                var id = warriorsD[i].id.split("_")[1];
                warriors.push(id);
            }
        }
        if(warriors.length === 0) {
            gameLog("ERROR: You have not selected any warriors to transfer");
            return false;
        }
        var m_warriors = ["transfer", "rest"];
        if(m_warriors.indexOf(value) == -1 && warriors.length > 1) {
            gameLog("ERROR: Only 1 warrior allowed for " + value + " action");
            return false;
        }
        switch(value) {
            case 'transfer':
                transfer(warriors);
                break;
            case 'heal':
                healWarrior('heal', warriors);
                break;
            case 'rest':
                healWarrior('rest', warriors);
                break;
            case 'offRest':
                offRest(warriors);
                break;
            case 'training':
                training(warriors, warriorsI);
                break;
            case 'changeType':
                changeType(warriors);
                break;
            default:
                
            break;
        }
    }
    function updatePage() {
        document.getElementsByName("action")[0].selectedIndex = 0;
    }
    function transfer(warriors) {
        var data = "model=ArmyCamp" + "&method=transfer" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                document.getElementById("overview").children[1].innerHTML = response[1];
                getCountdown();
            }       
        });
    }
    var intervalID = {
            
    };
    function getCountdown() {
        var data = "model=ArmyCamp" + "&method=getCountdown";
        ajaxJS(data, function(response) {
            if(response[0] != false) {
                data = response[1].split("||");
                data.pop();
                warriors = [];
                for(var i = 0; i < data.length; i++) {
                    warriors.push(data[i].split("|"));
                    var time = warriors[i][0];
                    var report = warriors[i][1];
                    var warrior = 'warrior' + [i];
                    intervalID[warrior] = setInterval(createCount(time, report, i),1000);
                }
            }
        });
    } 
    function createCount(time, report, i) {
        return function() {
            countdown(time, report, i);
        };
    }
    function countdown(time, report, id) {
        this.time = time * 1000;
        var now = new Date().getTime();
        var distance = this.time - now;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        var p = document.getElementsByClassName("countdown");
        p[id].innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        if (distance < 0 && report === "1"){
            console.log(intervalID['warrior' + id]);
            clearInterval(intervalID['warrior' + id]);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get training report");
            btn.appendChild(t);
            btn.addEventListener("click", function(){
                id = this.closest(".warrior").id.split("_")[1];
                updateTraining(id);
            });
            p[id].innerHTML = "";
            p[id].appendChild(btn);
        }
        else if (distance < 0) {
            clearInterval(intervalID['warrior' + id]);
            p[id].innerHTML = "No training in session";
        }
    }  
    function updateTraining(id) {
        console.log(id);
        var data = "model=UpdateTraining" + "&method=updateTraining" + "&warrior_id=" + id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                getCountdown();
                gameLog(response[1]);
            }       
        });
    }
    function healWarrior(type, warriors) {
        var item = false;
        var quantity = false;
        if(type == 'heal') {
            quantity = document.getElementById("quantity").value;
            var check = selectedCheck();
            if(check == false) {
                return false;
            }
            item = document.getElementById("selected").children[0].children[1].innerHTML.toLowerCase();
            var healing = {
                'yest-herb': 4,
                'healing': 3,
                'yas-herb': 2
            };
            if(healing[item] == 0) {
                gameLog("ERROR: Pick a valid item!");
                return false;
            }
            else if(healing[item] < quantity) {
                quantity = healing[item];
            }
        }
        var data = "model=ArmyCamp" + "&method=healWarrior" + "&item=" + item + "&quantity=" + quantity;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
            }       
        });
    }
    function offRest(warriors) {
        var data = "model=ArmyCamp" + "&method=offRest" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
            }       
        }); 
    }
    function training(warriors, warriorsI) {
        var select = document.getElementById("training").children[1];
        var type = select.children[select.selectedIndex].value;
        if(type.length < 0 || type == undefined) {
            gameLog("ERROR: Please select training type!");
            return false;
        }
        var data = "model=SetTraining" + "&method=setTraining" + "&warrior=" + warriors + "&type=" + type;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                console.log(response[1]);
                var rT = response[1].split("|");
                // Get correct div for the warrior
                var warriorsD = document.getElementsByClassName("warrior");
                var div = warriorsD[warriorsI[0]];
                div.querySelectorAll("TR").children[2].children[1].innerHTML = "Training";
                // Call getCountdown with data from ajaxP and set intervalID to be cleared later
                var interval_id = setInterval(getCountdown(rT['1'] * 1000, 0, warriorsI[0]), 1000);
                intervalID['warrior_' + warriorsI[0]] = interval_id;
            }
        });
    }
    function changeType(warriors) {
        var data = "model=ArmyCamp" + "&method=changeType" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                gameLog(response[1]);
                updatePage();
            }
        });
    }