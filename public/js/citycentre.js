    
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
            ajaxRequest = new XMLHttpRequest();
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    console.log(this.responseText);
                    if(this.responseText.search("ERROR") != -1) {
                        gameLog(this.responseText);
                    }
                    else {
                        gameLog(this.responseText);
                    }
                }   
            };
            ajaxRequest.open("POST", "/handlers/handler_p.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
        }
    }
    
    function changeArtefact() {
        var text = document.getElementById("selected").innerHTML;
        var text_comb = text.split("x");
        
        text_comb[1] = text_comb[1].split("(");
        var artefact = text_comb[1][0];
        var artefacts = ["energizer", "fighter"];
        if(artefacts.indexOf(artefact) == -1) {
            gameLog("That is not an artefact");
        }
        else {
            ajaxRequest = new XMLHttpRequest();
            var data = "model=citycentre" + "&method=changeArtefact" + "&artefact=" + artefact;
            ajaxRequest.onload = function () {
                if(this.readyState == 4 && this.status == 200) {
                    if(this.responseText.search("ERROR") != -1) {
                        gameLog(this.responseText);
                    }
                }
                else {
                    var data = this.responseText.split("|");
                    var artefactDiv = document.getElementById("artefact");
                    artefactDiv.children[0].img.src = "public/images/" + data[0] + '.jpg';
                    artefactDiv.children[1].innerHTML = "Current Artefact:" + data[0];
                    artefactDiv.children[2].innerHTML = "Uses left:" + data[1];
                    updateInventory();
                }
            };
            ajaxRequest.open('POST', "handlers/handler_p.php");
            ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            ajaxRequest.send(data);
                
        }
    }
    
    function buyPermits(val) {
        var amount = val;
        ajaxRequest = new XMLHttpRequest();
        var data = "model=citycentre" + "&method=buyPermits" + "&amount=" + amount;
        ajaxRequest.onload = function () {
            if(this.readyState == 4 && this.status == 200) {
                console.log(this.responseText);
                if(this.responseText.search("ERROR") != -1) {
                    gameLog(this.responseText);
                }
                else {
                    gameLog(this.responseText);
                    updateInventory();
                }
            }
        };
        ajaxRequest.open('POST', "handlers/handler_p.php");
        ajaxRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        ajaxRequest.send(data);
    }