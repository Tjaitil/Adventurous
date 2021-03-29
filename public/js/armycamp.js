    // warriorsIndex is the children index for the warriors
    console.log('armycamp.js');
    var warriorsIndex = [];
    // warriors is the id of the warriors selected
    var warriors = [];
    var intervalID = {      
    };
    if(document.getElementById("news_content").children[2] != null) {
        document.getElementById("actions").children[1].addEventListener("change", toogleActions);
        document.getElementById("actions").getElementsByTagName("button")[0].addEventListener("click", actions);
        selectItemEvent.addSelectEvent();
        warriorView(false, true);
        let buttons = document.getElementById("news_content_main_content").querySelectorAll("button");
        console.log(buttons);
        buttons[0].addEventListener("click", function() {
            game.fetchBuilding('ArmyMissions'); 
        });
        buttons[1].addEventListener("click", function() {
            game.fetchBuilding('Armory');
        });
    }
    function warriorView(selectedWarrior = false, set = false) {
        var warriors = document.getElementsByClassName("warrior");
        if(warriors.length > 0) {
            warriors = document.querySelectorAll(".warrior");
            let i = 0;
            warriors.forEach(function(element) {
            // Add event to each .warrior class
            if(selectedWarrior == false) {
                element.addEventListener('click', selectWarrior);
            }
            else if(warriorsIndex.indexOf(i) != -1) {
                element.addEventListener('click', selectWarrior);
            }
            i++;
            });    
            getCountdown(set);
            calculateSkillbar();
        }
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
        
        var divs = ["heal", "training"];
        
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
        // warriorsD is the node list of the warriors
        warriorsNodeList = document.getElementsByClassName("warrior");
        // warrriorsI is the div index of the warrior;
        warriorsIndex = [];
        // warriors is the id of warriors;
        warriors = [];
        for(var i = 0; i < warriorsNodeList.length; i++) {
            if(warriorsNodeList[i].style.border === "3px solid black") {
                warriorsIndex.push(i);
                var id = warriorsNodeList[i].id.split("_")[1];
                warriors.push(id);
            }
        }
        if(warriors.length === 0) {
            gameLog("ERROR: You have not selected any warriors to action");
            return false;
        }
        var m_warriors = ["transfer", "rest"];
        if(m_warriors.indexOf(value) == -1 && warriors.length > 1) {
            gameLog("ERROR: Only 1 warrior allowed for " + value + " action");
            return false;
        }
        switch(value) {
            case 'transfer':
                transfer();
                break;
            case 'heal':
                healWarrior('heal', warriors);
                break;
            case 'rest':
                healWarrior('rest', warriors);
                break;
            case 'offRest':
                offRest();
                break;
            case 'training':
                training(warriors, warriorsIndex);
                break;
            case 'changeType':
                changeType();
                break;
            default:
                
            break;
        }
        function transfer() {
            var data = "model=ArmyCamp" + "&method=transfer" + "&warriors=" + warriors;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    document.getElementById("overview").children[1].innerHTML = response[1];
                    updatePage();
                    getCountdown();
                    gameLog(response[1]);
                }       
            });
        }
    }
    function updatePage() {
        document.getElementsByName("action")[0].selectedIndex = 0;
        var data = "model=ArmyCamp" + "&method=getData" + "&warriors=" + warriors;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let splitArray = response[1].split('<div id=');
                splitArray.shift();
                for(let i = 0; i < splitArray.length; i++) {
                    let div = splitArray[i].split('"warrior">')[1].slice(0, -5);
                    document.getElementById("warriors").children[warriorsIndex[i]].innerHTML = div.trim();
                }
                warriorView(true);
            }
        });
    }
    function calculateSkillbar(warriorIndex = false) {
        var skillBars = document.getElementById("overview").querySelectorAll(".skill_bar"); 
        if(warriorIndex != false) {
            // skillBars2 holds the nodes from the specified warriorIndexes
            skillBars2 = [];
            for(var x = 0; x < warriorsIndex.length; x++) {
                skillBars2.push(skillBars[warriorsIndex[x]]);
            }
            skillBars = skillBars2;
        }
        let xp;
        let nextlevel;
        let width;
        let button = document.createElement("button");
        /*button.className = "warrior_level_up_button";*/
        button.innerHTML = "Level up " + '&#9650';
        let buttonInserted = [];
        for(var i = 0; i < skillBars.length; i++) {
            xp = skillBars[i].querySelectorAll(".progress_value1")[0].innerHTML;
            nextlevel = skillBars[i].querySelectorAll(".progress_value2")[0].innerHTML;
            width = xp / nextlevel * 100;
            // Check if there is button inserted in a warrior element, if present no new button needs to be created
            if(width === 100 && buttonInserted.indexOf(Math.floor(i / 4)) == -1) {
                skillBars[i].children[0].style.backgroundColor = "#008000";
                let divLevels = skillBars[i].closest(".levels");
                let id = i;
                button.addEventListener("click", function() {
                    warriorsIndex.push(Math.floor(id / 4));
                    warriors.push(
                    document.getElementById("warriors").children[Math.floor(id / 4)].querySelectorAll("p")[0].innerHTML.split("#")[1].trim()
                    );
                    levelUPWarrior();
                });
                divLevels.closest(".warrior").querySelectorAll(".warrior_level_up")[0].appendChild(button);
                console.log(divLevels.closest(".warrior").querySelectorAll(".warrior_level_up")[0]);
                buttonInserted.push(Math.floor(i / 4));
            }
            skillBars[i].children[0].style.width = width + "%";
        }
    }
    function getCountdown(set = false) {
        let data = "model=ArmyCamp" + "&method=getCountdown" + "&warriors=" + warriors;
        ajaxG(data, function(response) {
            if(response[0] != false) {
                console.log(response[1]);
                data = response[1].split("||");
                data.pop();
                // warriorsData holds the information gathered from db
                var warriorsData = [];
                let time;
                let report;
                let warrior;
                for(let i = 0; i < data.length; i++) {
                    warriorsData.push(data[i].split("|"));
                    time = warriorsData[i][0];
                    report = warriorsData[i][1];
                    if(set === true) {
                        warrior = 'warrior' + [i];
                        intervalID[warrior] = setInterval(createCount(time, report, i), 1000);
                    }
                    else {
                        warrior = 'warrior' + warriorsIndex[i];
                        intervalID[warrior] = setInterval(createCount(time, report, warriorsIndex[i]), 1000);
                    }
                }    
            }
        });
    } 
    function createCount(time, report, warriorIndex) {
        return function() {
            countdown(time, report, warriorIndex);
        };
    }
    function countdown(time, report, warriorIndex) {
        // warriorIndex is the child index of warrior
        this.time = time * 1000;
        var now = new Date().getTime();
        var distance = this.time - now;
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        var p = document.getElementsByClassName("countdown");
        p[warriorIndex].innerHTML = hours + "h " + minutes + "m " + seconds + "s ";
        if (distance < 0 && report === "1"){
            clearInterval(intervalID['warrior' + warriorIndex]);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get training report");
            btn.appendChild(t);
            btn.addEventListener("click", function(){
                warrior_id = this.closest(".warrior").id.split("_")[1];
                updateTraining(warrior_id);
                warriors.push(id);
                warriorsIndex.push(warriorIndex);
            });
            p[warriorIndex].innerHTML = "";
            p[warriorIndex].appendChild(btn);
        }
        else if (distance < 0) {
            clearInterval(intervalID['warrior' + warriorIndex]);
            p[warriorIndex].innerHTML = "";
        }
    }
    function levelUPWarrior() {
        if(warriors.length < 1 || warriorsIndex.length < 1) {
            return;
        }
        let data = "model=ArmyCamp" + "&method=checkWarriorLevel" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1].split("|");
                gameLog(responseText[1]);
                updatePage();
                newLevel.searchString(response[1]);
            }
        });
    }
    function updateTraining(warrior_id) {
        var data = "model=UpdateTraining" + "&method=updateTraining" + "&warrior_id=" + warrior_id;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1].split("|");
                updateCountdownTab();
                updatePage();
                gameLog(responseText[1]);
                newLevel.searchString(response[1]);
            }       
        });
    }
    function healWarrior(type, warriors) {
        var item = false;
        var quantity = false;
        if(type == 'heal') {
            if(warriors.length > 1) {
                
            }
            quantity = document.getElementById("quantity").value;
            var check = selectedCheck();
            if(check == false) {
                return false;
            }
            item = document.getElementById("selected").children[0].children[1].innerHTML.toLowerCase();
            var healing = {
                'yest-herb': 4,
                'healing potion': 3,
                'yas-herb': 2
            };
            // Check wether the item is a valid one
            if(healing[item] == 0) {
                gameLog("ERROR: Pick a valid item!", true);
                return false;
            }
            // Check if the quantity provided is higher than the quantity you would maximum need
            else if(healing[item] < quantity) {
                quantity = healing[item];
            }
        }
        var data = "model=ArmyCamp" + "&method=healWarrior" + "&item=" + item + "&quantity=" + quantity + "&warriors" + warriors;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
                updatePage();
            }       
        });
    }
    function offRest() {
        var data = "model=ArmyCamp" + "&method=offRest" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                for(let i = 0; i < warriors.length; i++) {
                    document.getElementById("warriors").children[warriorsIndex[i]].querySelectorAll("td")[3].innerText = "Nothing special";
                }
            }       
        }); 
    }
    function training(warriors, warriorsI) {
        var select = document.getElementById("training").children[1];
        var type = select.children[select.selectedIndex].value;
        if(type.length < 0 || type == undefined) {
            gameLog("ERROR: Please select training type!", true);
            return false;
        }
        var data = "model=SetTraining" + "&method=setTraining" + "&warrior=" + warriors + "&type=" + type;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1].split("|");
                updateCountdownTab();
                updatePage();
                newLevel.searchString(response[1]);
                gameLog(responseText[1]);
            }
        });
    }
    function changeType() {
        var data = "model=ArmyCamp" + "&method=changeType" + "&warriors=" + warriors;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory();
                gameLog(response[1]);
                let type = response[1].split("to ")[1].split(" for")[0].trim();
                for(var i = 0; i < warriors.length; i++) {
                    let img = document.getElementById("warriors").children[warriorsIndex[i]].querySelectorAll("img");
                    img.src = "public/images/" + type + " icon.png";
                }
            }
        });
    }