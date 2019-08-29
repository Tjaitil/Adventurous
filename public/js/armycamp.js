
    document.getElementById("actions").children[1].addEventListener("change", toogleActions);
    document.getElementById("actions").getElementsByTagName("button")[0].addEventListener("click", actions);
    console.log(document.getElementById("actions").getElementsByTagName("button")[0]);
    window.onload = function() {
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
    };
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
        var form = document.getElementById("calc_form").children[0].children;
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
        }
        var data = "model=CombatCalculator" + "&method=calculate" + "&form_data=" + JSON.stringify(form_data);
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                show('calculator');
                document.getElementById("calc_form").style = "display: none";
                var div = document.getElementById("calc_result");
                div.style = "visibility: visible";
                div.innerHTML += this.responseText;   
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    function toogleActions() {
        var select = document.getElementsByName("action")[0];
        var value = select.options[select.selectedIndex].value;
        
        var divs = ["transfer", "heal"];
        
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
        var warriors = [];
        for(var i = 0; i < warriorsD.length; i++) {
            if(warriorsD[i].style.border === "3px solid black") {
                var id = warriorsD[i].id.split("_")[1];
                warriors.push(id);
            }
        }
        var checkbox = document.getElementById("overview").getElementsByTagName("INPUT");
        
        if(checkbox.length === 0) {
            gameLog("ERROR: You have not selected any warriors to transfer");
            return false;
        }
        /*for(var i = 0; i < checkbox.length; i++) {
            if(checkbox[i].type === 'checkbox' && checkbox[i].checked == true) {
                warriors.push(checkbox[i].value);
            }
        }*/
        if(warriors.length === 0) {
            return;
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
                training(warriors);
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
        ajaxRequest = new XMLHttpRequest();
        var data = "model=ArmyCamp" + "&method=transfer" + "&warriors=" + warriors;
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR:") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    document.getElementById("overview").children[1].innerHTML = this.responseText;
                    getCountdown();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    var intervalID = {
            
    };
    function getCountdown() { 
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                data = this.responseText.split("||");
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
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=ArmyCamp" + "&method=getCountdown");
        ajaxRequest.send();
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
            clearInterval(countdown);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get training report");
            btn.appendChild(t);
            btn.addEventListener("click", function(){
                id = this.closest(".warrior").id.split("_")[1];
                updateTraining(id);
            });
            p[id].appendChild(btn);
        }
        else if (distance < 0) {
            clearInterval(intervalID['warrior' + id]);
            p[id].innerHTML = "No training in session";
        }
    }  
    function updateTraining(id) {
        var data = "model=UpdateTraining" + "&method=updateTraining" + "&warrior_id=" + id;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function() {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    getCountdown();
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open("POST", "/handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    function healWarrior(type, warriors) {
        console.log(warriors);
        var data = "model=ArmyCamp" + "&method=healWarrior";
        if(type == 'heal') {
            var quantity = document.getElementById("quantity").value;
            var check = selectedCheck();
            if(check == false) {
                return false;
            }
            var item = document.getElementById("selected").children[0].children[1].innerHTML.toLowerCase();
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
            data += "&type=item" + "&warriors=" + warriors[0] + "&item=" + item + "&quantity=" + quantity;
        }
        else {
            data += "&type=" + 'rest' + "&warriors=" + warriors;
        }
       ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);
                    return false;
                }
                else {
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }
    function offRest(warriors) {
        
        var data = "model=ArmyCamp" + "&method=offRest" + "&warriors=" + warriors;
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                if(this.responseText.indexOf("ERROR:") != -1) {
                    gameLog(this.responseText);
                    return false;
                }
                else {
                    gameLog(this.responseText);
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
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