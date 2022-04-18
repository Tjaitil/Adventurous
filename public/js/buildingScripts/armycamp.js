    var warriors = {
        init() {
            let warriorsNodeList = document.getElementsByClassName("warrior");
            for(let i = 0; i < warriorsNodeList.length; i++) {
                this.data.push({
                    cIndex: i,
                    id: parseInt(warriorsNodeList[i].id.split("_")[1]),
                    selected: false,
                    intervalID: null,
                    countdownData: null,
                    calculateSkillbar
                })   
            }
        },
        selectedIds: [],
        data: [],
        toggleSelect(id) {
            this.data.forEach((element, index) => {
                if(element.id === id ) {
                    this.data[index].selected = !element.selected;
                }
            })
        },
        getSelectedIds(all = false) {
            this.selectedIds = [];
            if(all === true) {
                this.data.forEach(element => this.selectedIds.push(element.id));
            }
            else {
                this.data.forEach(element => {
                    console.log(element.selected);
                    if(element.selected === true) this.selectedIds.push(element.id);
                });
            }
        },
        unselectDiv() {
            let warriorsNodeList = document.getElementsByClassName("warrior");
            for(const i of warriorsNodeList) {
                i.style.border = "none";
            }
        },
        resetSelected() {
            this.data.forEach((element, index) => this.data[index].selected = false);
            this.selectedIds = [];
        }
    }
    var singleWarriorAction = ["transfer", "training", "change type", "heal"];
    function toggleHiglightedWarriorDiv() {
        if(event.target.tagName === 'BUTTON') {
            return false;
        }
        let DOMelement = event.target.closest(".warrior")
        let id = parseInt(DOMelement.id.split("_")[1]);
        if(DOMelement.style.borderLeftWidth === "3px") {
            DOMelement.style.border = "";
        }
        else {
            DOMelement.style.border = "3px ridge #5f4121";        
        }
        warriors.toggleSelect(id);
        checkAction();
    }
    var intervalID = {      
    };
    if(document.getElementById("news_content").children[2] != null) {
        document.getElementById("actions").children[1].addEventListener("change", toogleActions);
        document.getElementById("actions").getElementsByTagName("button")[0].addEventListener("click", actions);
        document.getElementsByName("action")[0].addEventListener("change", () => checkAction());
        selectItemEvent.addSelectEvent();
        (() => warriors.init())();
        getInitCountdown();
        warriors.data.forEach(element => {
            if(warriors.selectedIds.includes(element.id)) {
                element.calculateSkillbar(element.cIndex);
            }
        });
        var warriorsDivs = document.querySelectorAll(".warrior");
        warriorsDivs.forEach((element, index) => {
            // Add event to each .warrior class
            warriorsDivs[index].addEventListener('click', () => toggleHiglightedWarriorDiv());
        });
        let buttons = document.getElementById("news_content_main_content").querySelectorAll("button");
        buttons[0].addEventListener("click", function() {
            inputHandler.fetchBuilding('ArmyMissions'); 
        });
        buttons[1].addEventListener("click", function() {
            inputHandler.fetchBuilding('Armory');
        });
    }
    function checkAction() {
        let select = document.getElementsByName("action")[0];
        let val = select.children[select.selectedIndex].value;
        let warning =  document.getElementById("multiple-warrior-action-warning");
        let amount = warriors.data.filter(element => (element.selected === true)).length;
        if(singleWarriorAction.includes(val) && amount > 1) {
            warning.style.visibility = "visible";
        } else {
            warning.style.visibility = "hidden";
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
        let form_data = {};
        form_data['warriors'] = ["1", "2", "3"];
        form_data['daqloon'] = 3;
        let data = "model=CombatCalculator" + "&method=calculate" + "&form_data=" + JSON.stringify(form_data) + 
                    "&route=" + "db";
        ajaxP(data, function(response) {
            console.log(response);
            if(response[0] !== false) {
                console.log(response[1]);
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
    function updatePage() {
        document.getElementsByName("action")[0].selectedIndex = 0;
        let selected = warriors.selectedIds;
        if(warriors.selectedIds === 0) return false;
        let data = "model=ArmyCamp" + "&method=getData" + "&warriors=" + JSON.stringify(selected);
        ajaxG(data, function(response) {
            // console.log(response);
            let responseText = response[1];
            updateCountdownTab();
            let warriorsDiv = document.getElementById("warriors").children;
            let template;
            let div;
            if(typeof(responseText.html) === "string") {
                let res = responseText.html;
                responseText.html = [res];
            }
            let data = {
                html: {

                },
                warriorCountdowns: {

                }
            };
            // Index data by warrior-id
            selected.forEach((id, index) => {
                    data.html[id] = responseText.html[index];
                    data.warriorCountdowns[id] = responseText.warriorCountdowns[id];
            });
            console.log(warriors.selectedIds);
            warriors.data.forEach((element, index) => {
                if(warriors.selectedIds.includes(element.id)) {
                    template = document.createElement("template");
                    template.innerHTML = data.html[element.id].trim();
                    div = template.content.firstChild;
                    document.getElementById("warriors").replaceChild(div, warriorsDiv[element.cIndex]);
                    div.addEventListener('click', () => toggleHiglightedWarriorDiv());
                    createCount(data.warriorCountdowns);
                    element.calculateSkillbar(element.cIndex);
                }
            });
            warriors.resetSelected();
        });
    }
    function calculateSkillbar(warriorIndex) {
        var skillBars = document.getElementById("overview").querySelectorAll(".skill_bar"); 
        let width;
        let nodeIndex
        for(let i = 0; i < skillBars.length; i++) {
            nodeIndex = Math.floor(i / 4);
            if(warriorIndex !== false && nodeIndex !== warriorIndex) {
                continue;
            }
            skillBars[i].querySelectorAll(".progressBarOverlay")[0].style.width = 0 + "%";
            width = progressBar.calculateProgress(skillBars[i], false, false, true, true);
            if(width === 100) {
                if(document.querySelectorAll(".warrior_level_up")[nodeIndex].children.length === 0) {
                    button = document.createElement("button");
                    button.innerHTML = "Level up " + '&#9650';
                    button.addEventListener("click", function() {
                        // warriorsIndex.push(Math.floor(id / 4));
                        let warrior_id = getWarriorId(this);
                        levelUPWarrior(warrior_id);
                    });
                    document.querySelectorAll(".warrior_level_up")[nodeIndex].appendChild(button);
                }
            } 
        }
    }
    function getInitCountdown() {
        warriors.getSelectedIds(true);
        let data = "model=ArmyCamp" + "&method=getCountdown" + "&warriors=" + JSON.stringify(warriors.selectedIds);
        ajaxG(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1];
                createCount(responseText.warriorCountdowns);
            }
        });
    }
    function createCount(data) {
        console.log(data);
        console.log(warriors.selectedIds);
        warriors.data.forEach((element, index) => {
            if(warriors.selectedIds.includes(element.id)) {
                warriors.data[index].time = data[element.id].training_countdown;
                warriors.data[index].report = data[element.id].fetch_report;
                warriors.data[index].date = data[element.id].date;
                warriors.data[index].intervalID = setInterval(() => countdown(element), 1000);
            }
        }); 
    }
    function countdown(warrior) {
        // console.log(time, report, warriorIndex);
        // warriorIndex is the child index of warrior
        this.time = warrior.date * 1000;
        let report = warrior.report;
        let now = new Date().getTime();
        let distance = this.time - now;
        let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        let seconds = Math.floor((distance % (1000 * 60)) / 1000);
        let p = document.getElementsByClassName("countdown");
        if(!p[warrior.cIndex]) {
            clearInterval(warrior.intervalID);
            return;
        }
        if (distance < 0 && report === "1"){
            // warriorIndex + 1 is the id of the warrior
            clearInterval(warrior.intervalID);
            var btn = document.createElement("BUTTON");
            var t = document.createTextNode("Get report");
            btn.appendChild(t);
            btn.addEventListener("click", function(){
                warrior_id = this.closest(".warrior").id.split("_")[1];
                let warrior = warriors.data.find(element => element.id === warrior_id);
                if(warrior === "undefined") {
                    return false;
                }
                updateTraining(parseInt(warrior_id));
            });
            p[warrior.cIndex].innerHTML = "";
            p[warrior.cIndex].appendChild(btn);
        }
        else if (distance < 0) {
            // warriorIndex + 1 is the id of the warrior
            clearInterval(warrior.intervalID);
            p[warrior.cIndex].innerHTML = "";
        }
        else {
            p[warrior.cIndex].innerHTML = hours + "h " + minutes + "m " + seconds + "s";;
        }
    }
    function getWarriorId(element) {
        return parseInt(element.closest(".warrior").id.split("_")[1]);
    }
    function actions(action = false) {
        var select = document.getElementsByName("action")[0];
        var action = select.options[select.selectedIndex].value;
        if(action.length === 0) {
            gameLogger.addMessage("ERROR: Select a action to perform");
            gameLogger.logMessages();
            return false;
        }
        warriors.getSelectedIds();
        let selectedIds = warriors.selectedIds;
        if(selectedIds.length === 0) {
            gameLogger.addMessage("ERROR: You have not selected any warriors for action");
            gameLogger.logMessages();
            return false;
        }
        if(singleWarriorAction.includes(action) == -1 && selectedIds.length > 1) {
            gameLogger.addMessage("ERROR: Only 1 warrior allowed for " + action + " action");
            gameLogger.logMessages();
            return false;
        }
        selectedIds = JSON.stringify(selectedIds);
        let update = false;
        let data;
        switch(action) {
            case 'transfer':
                // Single warrior action
                data = "model=ArmyCamp" + "&method=transfer" + "&warriors=" + selectedIds;
                update = true;
                break;
            case 'heal':
                let item = false;
                let quantity = false;
                quantity = document.getElementById("selected_amount").value;
                if(selectedCheck() === false) return false;
                item = document.getElementById("selected").children[0].children[1].innerHTML.toLowerCase();
                let healing = {
                    'yest-herb': 4,
                    'healing potion': 3,
                    'yas-herb': 2
                };
                // Check wether the item is a valid one
                if(healing[item] == 0) {
                    gameLogger.addMessage("ERROR: Pick a valid item!");
                    gameLogger.logMessages();
                }
                // Check if the quantity provided is higher than the quantity you would maximum need
                else if(healing[item] < quantity) {
                    quantity = healing[item];
                    return false;
                }
                data = "model=ArmyCamp" + "&method=healWarrior" + "&actionType=heal" + "&item=" + item + "&amount=" + quantity + 
                        "&warriors=" + selectedIds;
                update = true;
                break;
            case 'rest':
                data = "model=ArmyCamp" + "&method=rest" + "&warriors=" + selectedIds;
                update = true;
                break;
            case 'offRest':
                data = "model=ArmyCamp" + "&method=offRest" + "&warriors=" + selectedIds;
                ajaxP(data, function(response) {
                    if(response[0] !== false) {
                        warriors.data.forEach(element => {
                            if(selectedIds.includes(element.id)) {
                                document.getElementsByClassName("warrior-status")[element.cIndex].innerText = "Idle";
                                document.getElementsByClassName("warrior-health")[element.cIndex].innerText = "";
                            }
                        })
                    }       
                }); 
                break;
            case 'training':
                let select = document.getElementById("training").children[1];
                let type = select.children[select.selectedIndex].value;
                if(type.length < 0 || type == undefined) {
                    gameLogger.addMessage("ERROR: Please select training type!");
                    gameLogger.logMessages();
                    return false;
                }
                data = "model=SetTraining" + "&method=setTraining" + "&warrior=" + selectedIds + "&type=" + type;
                ajaxP(data, function(response) {
                    if(response[0] !== false) {
                        updateCountdownTab();
                        updatePage();
                    }
                });
                break;
            case 'changeType':
                data = "model=ArmyCamp" + "&method=changeType" + "&warrior=" + selectedIds[0];
                ajaxP(data, function(response) {
                    if(response[0] !== false) {
                        updateInventory();
                        let responseText = response[0];
                        let type = responseText.warrior_type;                
                        warriors.data.forEach(element => {
                            if(selectedIds.includes(element.id)) {
                                document.getElementsByClassName("warrior").children[element.cIndex].querySelectorAll("img");
                                img.src = "public/images/" + type + " icon.png";
                            }
                        });
                    }
                });
                break;
            default:

            break;
        }
        if(update === true) {
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    updatePage();
                }       
            });
        }
    }
    function levelUPWarrior(id) {
        let warrior_id = parseInt(id);
        warriors.resetSelected();
        warriors.toggleSelect(warrior_id);
        warriors.getSelectedIds();
        let data = "model=ArmyCamp" + "&method=checkWarriorLevel" + "&warriors=" + warrior_id;
        ajaxP(data, function(response) {
            if(response[0] != false) {
                updatePage();
            }
        });
    }
    function updateTraining(id) {
        let warrior_id = parseInt(id);
        warriors.resetSelected();
        warriors.toggleSelect(warrior_id);
        warriors.getSelectedIds();
        if(warriors.selectedIds.length === 0) return false;
        let data = "model=UpdateTraining" + "&method=updateTraining" + "&warrior_id=" + warriors.selectedIds[0];
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateCountdownTab();
                updatePage();
            }       
        });
    }