    
    function transfer() {
        var checkbox = document.getElementById("overview").getElementsByTagName("INPUT");
        var warriors = [];
        if(checkbox.length === 0) {
            gameLog("You have not selected any warriors to transfer");
            return false;
        }
        for(var i = 0; i < checkbox.length; i++) {
            if(checkbox[i].type === 'checkbox' && checkbox[i].checked == true) {
                warriors.push(checkbox[i].value);
            }
        }
        if(warriors.length === 0) {
            return;
        }
        
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
                    setInterval(createCount(time, report, i),1000);
                }
            }
        };
        ajaxRequest.open('GET', "handlers/handler_js.php?model=ArmyCamp" + "&method=getCountdown");
        ajaxRequest.send();
    }
    
    window.onload = getCountdown();
    
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
        document.getElementById("overview").rows[id + 1].cells[7].innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
        if (distance < 0 && report === "1"){
            clearInterval(countdown);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get training report");
            btn.appendChild(t);
            /*btn.click = function() {updateTraining(this.cellIndex);};*/
            btn.addEventListener("click", function(){
                var tr = this.parentNode.parentNode;
                var tr_num = tr.rowIndex;
                updateTraining(tr_num);
            });
            document.getElementById("overview").rows[id + 1].cells[7].appendChild(btn);
        }
        else if (distance < 0) {
            clearInterval(countdown);
            document.getElementById("overview").rows[id + 1].cells[7].innerHTML = "No training in session";
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
        console.log(inputs);
        console.log(newFields);
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