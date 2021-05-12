    if(document.getElementById("news_content").children[2] != null) {
       // Add events to buttons
       document.getElementById("profiency").querySelectorAll("button")[0].addEventListener("click", function () {
            alertMessage('citycentre');
        });
        var keep_buttons = document.getElementById("keep").querySelectorAll("button");
        keep_buttons[0].addEventListener("click", changeArtefact);
        keep_buttons[1].addEventListener("click", newArtefact);
        document.getElementById("miner_permits").querySelectorAll("button")[0].addEventListener("click", buyPermits);
        document.getElementById("efficiency").querySelectorAll("button")[0].addEventListener("click", upgradeEffiency);
        let unlock_items_button = document.getElementById("unlock_items").querySelectorAll("button");
        unlock_items_button.forEach(function(element) {
            // ... code code code for this one element
            element.addEventListener('click', unlockArmorItems);
        });
    }
    function unlockArmorItems() {
        let button = event.target;
        let item = button.closest("tr").children[0].innerText.toLowerCase().trim();
        console.log(item);
        if(["frajrite items", "wujkin items"].indexOf(item) == -1) {
            gameLog("This item cannot be unlocked");
            return false;
        }
        let data = "model=CityCentreA" + "&method=unlockArmorItems" + "&type=" + item; 
        ajaxP(data, function(response) {
            if(response[0] != false) {
                let responseText = response[1].split("|");
                updateInventory();
                gameLog(responseText[0]);
                button.className = "button_disabled";
                button.innerText = "Unlocked";
            }
        });
    }
    function changeProfiency() {
        console.log('hello');
        var select = document.getElementById("profiency_select");
        var val = select.value;
        if(!val) {
            gameLog("Please select a profiency!");
            return false;
        }
        /*var data = "model=Profiency" + "&method=changeProfiency" + "&newProfiency=" + val;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
            }       
        });*/
    }
    function changeArtefact() {
        var itemData = selectedCheck(false);
        console.log(itemData);
        if(itemData.length === false) {
            return false;
        }
        var artefacts = ["harvester", "prospector", "collector", "healer", "rewardist", "fighter"];
        if(artefacts.indexOf(itemData[0]) == -1) {
            gameLog("That is not an artefact");
        }
        else {
            var data = "model=Artefact" + "&method=changeArtefact" + "&artefact=" + itemData[0];
            ajaxP(data, function(response) {
                console.log(response);
                if(response[0] !== false) {
                    var data = response[1].split("|");
                    var artefactDiv = document.getElementById("artefact");
                    artefact = data[0].split("|")[0].trim();
                    artefactDiv.children[0].src = "public/images/" + data[0] + '.jpg';
                    artefactDiv.querySelectorAll("p")[0].innerHTML = "Current Artefact:" + jsUcfirst(data[0]); 
                    updateInventory();
                }       
            });   
        }
    }
    function buyPermits() {
        var amount = 50;
        let permit_div = document.getElementById("miner_permits").querySelectorAll("p")[0];
        var permits = Number(permit_div.innerHTML.split(":")[1].trim()) + 50;
        var data = "model=CityCentreA" + "&method=buyPermits" + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
                permit_div.innerHTML = "Current permits: " + permits;
                updateInventory();
            }       
        });
    }
    function newArtefact() {
        var data = "model=Artefact" + "&method=newArtefact";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                var responseText = response[1].split("|");
                openNews('Waiting');
                setTimeout( function() {
                        document.getElementById("news_content").innerText = "";
                        var img = document.createElement("IMG");
                        img.href = "/public/img/" + responseText[1] + ".png";
                        img.style = "width: 50px; height: 50px; margin-left: 10px";
                        openNews(responseText[0] + responseText[1]);
                        openNews(img);
                    }, 3000);
            }
        });
    }
    function setArtefact() {
        var data = "model=Artefact" + "&method=setArtefact";
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                    
            }
        });
    }
    function upgradeEffiency() {
        var tr = event.target.closest("tr");
        var skill;
        if(tr.previousSibling != null) {
            skill = "farmer";
        }
        else {
            skill = "miner";
        }
        var data = "model=Workers" + "&method=upgradeEffiency" + "&skill=" + skill;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory();
                var responseText = response[1].split("|");
                gameLog(responseText[0]);
                tr.children[1].innerHTML = responseText[1];
                tr.children[2].childNodes[0] = responseText[1] * 150;
            }    
        });
    }