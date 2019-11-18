    window.addEventListener("load", function() {
       // Add events to buttons
       document.getElementById("profiency").querySelectorAll("button")[0].addEventListener("click", changeProfiency);
       var keep_buttons = document.getElementById("keep").querySelectorAll("button");
       keep_buttons[0].addEventListener("click", changeArtefact);
       keep_buttons[1].addEventListener("click", newArtefact);
       document.getElementById("permits").querySelectorAll("button")[0].addEventListener("click", buyPermits);
       
    });
    
    
    function show(element) {
        var divs = ["profiency", "keep", "permits"];
        
        for(var i = 0; i < divs.length; i++) {
            if(divs[i] == element) {
                document.getElementById(divs[i]).style = "display: inline";
            }
            else {
                document.getElementById(divs[i]).style = "display: none";
            }
        }
    }
    function changeProfiency() {
        var message = 'Beware that changing profiency may result in lowering levels \n and no access to profiency specific activites';
        var link = '\n Read more on /gameguide/profiency \n Are you sure you want to continue?';
        var conf = confirm(message + link);
        if(conf !== true) {
            return false;
        }
        else {
            var select = document.getElementById("profiency_select");
            var val = select.value;
            var data = "model=profiency" + "&method=changeProfiency" + "&newProfiency=" + val;
            ajaxP(data, function(response) {
                if(response[0] !== false) {
                    gameLog(response[1]);
                }       
            });
        }
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
                    artefactDiv.querySelectorAll("p")[0].innerHTML = "Current Artefact:" + data[0];
                    updateInventory();
                }       
            });   
        }
    }
    function buyPermits() {
        var amount = 50;
        var data = "model=citycentre" + "&method=buyPermits" + "&amount=" + amount;
        ajaxP(data, function(response) {
            if(response[0] !== false) {
                gameLog(response[1]);
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
                        document.getElementById("content").innerText = "";
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