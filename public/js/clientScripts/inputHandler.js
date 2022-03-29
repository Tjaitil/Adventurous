const inputHandler = {
    checkBuilding(mouseX = false, mouseY = false) {
        if(tutorial.onGoing) {
            gameLogger.addMessage("This building can not be accessed on tutorial island");
            gameLogger.logMessages();
        }
        if (game.properties.inBuilding != true && game.properties.device == "pc") {
            for (i = 0; i < gamePieces.buildings.length; i++) {
                let object = gamePieces.buildings[i];
                if (gamePieces.player.ypos > object.diameterUp && gamePieces.player.ypos < object.diameterDown &&
                    gamePieces.player.xpos > object.diameterLeft && gamePieces.player.xpos < object.diameterRight &&
                    Math.abs(gamePieces.player.ypos - object.diameterDown) < 32) {
                    if (game.properties.inBuilding == false) {
                        inputHandler.fetchBuilding(object.src.split(".png")[0]);
                    }
                    break;
                }
            }
        }
        else if (game.properties.inBuilding != true && game.properties.device == "mobile") {
            console.log('check building');
            let element = document.getElementById("text_canvas");
            let ElementPos = element.getBoundingClientRect();
            // Remove elementPos of the canvas so that 0.0 is in up-left corner
            mouseY = mouseY - ElementPos.top;
            mouseX = mouseX - ElementPos.left;
            let x = mouseX + (gamePieces.player.xpos - (game.properties.canvasWidth / 2) + 32);
            let y = mouseY + (gamePieces.player.ypos - (game.properties.canvasHeight / 2));
            let result = false;
            for (i = 0; i < gamePieces.buildings.length; i++) {
                object = gamePieces.buildings[i];
                if (y > object.diameterUp && y < object.diameterDown &&
                    x > object.diameterLeft && x < object.diameterRight &&
                    Math.abs(gamePieces.player.ypos - object.diameterDown) < 32) {
                    result = true;
                    inputHandler.fetchBuilding(object.src.split(".png")[0]);
                    break;
                }
            }
            return result;
        }
    },
    fetchBuilding(building = false) {
        game.properties.inBuilding = true;
        conversation.endConversation();
        if (building == false) {
            building = 'test';
        }
        let h = document.createElement("h2");
        h.innerText = "Loading...";
        h.id = "loading_message";
        openNews(h);
    
        ajaxRequest = new XMLHttpRequest();
        ajaxRequest.onload = function () {
            if (this.readyState == 4 && this.status == 200) {
                console.log(building);
                console.log(this.responseText);
                if (this.responseText.indexOf("ERROR") != -1) {
                    gameLogger.addMessage("ERROR: Something unexpected happened, please try again");
                    gameLogger.logMessages();
                    closeNews();
                    return false;
                }
                var responseText = this.responseText.split("|");
                var link;
                if (document.getElementById("fetch_stylesheet") === null) {
                    link = document.createElement("link");
                    link.type = "text/css";
                    link.rel = "stylesheet";
                    link.setAttribute("id", "fetch_stylesheet");
                    link.href = "public/css/" + responseText[0].trim();
                }
                else {
                    link = document.getElementById("fetch_stylesheet");
                    link.href = "public/css/" + responseText[0].trim();
                }
                document.getElementsByTagName("head")[0].appendChild(link);
                var script;
                var script2;
                var scripts = responseText[1].split("%");
                openNews(responseText[2], true);
                game.properties.building = building;
                if (document.getElementById("fetch_script") === null) {
                    script = document.createElement("script");
                    script.src = "public/js/" + scripts[0].trim();
                    script.id = "fetch_script";
                    document.getElementsByTagName("section")[0].appendChild(script);
                }
                else {
                    script = document.createElement("script");
                    document.getElementById("fetch_script");
                    script.src = "public/js/" + scripts[0].trim();
                    script.id = "fetch_script";
                    document.getElementsByTagName("section")[0].replaceChild(script, document.querySelector("#fetch_script"));
                }
                if (document.getElementById("fetch_script2") === null && scripts.length > 1) {
                    script2 = document.createElement("script");
                    script2.src = "public/js/" + scripts[1].trim();
                    script2.id = "fetch_script2";
                }
                else if (scripts.length > 1) {
                    script2 = document.getElementById("fetch_script2");
                    script2.src = "public/js/" + scripts[1].trim();
                }
                if (script2 !== undefined) {
                    document.getElementsByTagName("section")[0].appendChild(script2);
                }
                // Add events to item elements
                itemTitle.addItemClassEvents();
            }
        };
        ajaxRequest.open('GET', "handlers/handler_v.php?" + "&building=" + building.trim());
        ajaxRequest.send();
    },
    checkCharacter() {
        for (let i = 0; i < gamePieces.visibleObjects.length; i++) {
            if (Math.abs(gamePieces.player.xpos - gamePieces.visibleObjects[i].x) < 32 &&
                Math.abs(gamePieces.player.ypos - gamePieces.visibleObjects[i].y) < 32 && 
                gamePieces.visibleObjects[i].type === "character") {
                if(gamePieces.visibleObjects[i].src.split(".png")[0] === "hassen") {
                    tutorial.checkStep();
                } else if(tutorial.onGoing && gamePieces.visibleObjects[i].src.split(".png")[0] !== "tutorial_sailor") {
                    gameLogger.addMessage("That person is not interested in talking to you now");
                    gameLogger.logMessages();
                } else {
                    conversation.loadConversation(gamePieces.visibleObjects[i].displayName);
                }
                break;
            }
        }
    }
}