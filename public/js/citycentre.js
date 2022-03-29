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
            gameLogger.addMessage("This item cannot be unlocked");
            gameLogger.logMessages();
            return false;
        }
        let data = "model=CityCentreA" + "&method=unlockArmorItems" + "&type=" + item; 
        ajaxP(data, function(response) {
            if(response[0] != false) {
                updateInventory();
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
            gameLogger.addMessage("Please select a profiency!"); 
            gameLogger.logMessages();
            return false;
        }
        /*var data = "model=Profiency" + "&method=changeProfiency" + "&newProfiency=" + val;
        ajaxP(data, function(response) {
            if(response[0] !== false) {

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
            gameLogger.addMessage("That is not an artefact");
            gameLogger.logMessages();
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
        let amount = 50;
        let selectedLocation = document.querySelector('input[name="permit_location"]:checked').value;
        if(!selectedLocation) {
            gameLogger.addMessage("ERROR Select a location to buy permits");
            gameLogger.logMessages();
            return false;
        }
        let data = "model=CityCentreA" + "&method=buyPermits" + "&amount=" + amount + "&selectedLocation=" + selectedLocation;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                let responseText = response[1];
                let spans = document.getElementById("miner_permits").querySelectorAll("span");
                if(selectedLocation === "golbak") {
                    spans[0].innerText = responseText.permits; 
                } else {
                    spans[1].innerText = responseText.permits; 
                }
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
        let tr = event.target.closest("tr");
        let skill;
        if(tr.previousSibling != null) {
            skill = "farmer";
        }
        else {
            skill = "miner";
        }
        let data = "model=Workers" + "&method=upgradeEffiency" + "&skill=" + skill;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                updateInventory();
                let responseText = response[1];
                tr.children[1].innerHTML = responseText[1].effiencyLevel;
                tr.children[2].childNodes[0] = responseText[1].effiencyLevel * 150;
            }    
        });
    }