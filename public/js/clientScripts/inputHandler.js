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
            let x = mouseX + (gamePieces.player.xpos - (viewport.width / 2) + 32);
            let y = mouseY + (gamePieces.player.ypos - (viewport.height / 2));
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
    currentBuildingModule: null,
    async fetchBuilding(building) {
        game.properties.inBuilding = true;
        conversation.endConversation();
        let h = document.createElement("h1");
        h.innerText = "Loading...";
        h.id = "loading_message";
        openNews(h);
    
        building = building.trim();
        let module = 'bakery';
        await fetch('handlers/handler_v.php?' + new URLSearchParams({'building': building})).
            then(response => {
                if(!response.ok) throw new Error("Something unexpected happened. Please try again");
                return response.text();
            })
            .then(async (data) => { 
                game.properties.building = building;
                let dataArray = data.split("|");
                let css = dataArray[0].trim();
                let script = dataArray[1];
                let html = dataArray[2];
                if(css.length > 2 || css !== "#") {
                    link = document.createElement("link");
                    link.type = "text/css";
                    link.rel = "stylesheet";
                    link.href = "public/css/" + css;
                    document.getElementsByTagName("head")[0].appendChild(link);
                }
                openNews(html, true);
                itemTitle.addItemClassEvents();

                const module = await scriptLoader.importBuildingModule(script)
                .then(data => {
                    this.currentBuildingModule = data;
                    if(this.currentBuildingModule.default.init) {
                        this.currentBuildingModule.default.init();
                    } 
                });
            })
            .catch(error => {
                closeNews();
                alert(error);
                return;
            })
        return building;
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