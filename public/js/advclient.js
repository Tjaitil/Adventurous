scriptLoader.loadScript([
    'inputHandler', 'help', 'collision', 'gameEventHandler', 'map', 'canvasText', 'conversation',
    'controls', 'spritesContainer', 'testScripts', 'pause', 'inventory', 'tutorial', 'hunger', 'travel']);
const game = {};
CookieTicket = {
    checkCookieTicket(cookieNoob = "getOut") {
        var today = new Date();
        var cookieTicket;
        if(CookieTicket.sweetCookie === null) {
            cookieTicket = today.getMonth() + today.getDate() + "|";
            for(var i = 0; i < 10; i++) {
                cookieTicket += Math.floor(Math.random() * (10 - 1) + 1);
            }
            CookieTicket.sweetCookie = cookieTicket;
        }
        else {
            cookieTicket = CookieTicket.sweetCookie;
        }
        let data = "model=cookieMaker" + "&method=yummyCookies" + "&cookieTicket=" + cookieTicket + "&cookieNoob=" + cookieNoob;
        ajaxP(data, function(response) {
            console.log(response);
            let responseText = response[1].status;
            if(responseText.status === "false") {
                // Session check return false, go to logout
                alert("A newer session is ongoing. You are being logged out from this session");
                setTimeout(() => {
                    location.href = "/logout";
                }, 5000);
            }
            else {
                return;
            }
        });
    },
    disposeGarbage() {
        let ego = event.target.innerText;
        if(ego === "") {
            checkCookieTicket();
        }
        else {
            burntCookie();
        }
    },
    burntCookie() {
        let data = "model=cookieMaker" + "&method=delicCookies" + "&cookieTicket=" + CookieTicket.sweetCookie;
        ajaxP(data, function(response) {
            console.log(response);
            if(response[1] == false) {
                window.cancelAnimationFrame(game.properties.requestId);
                game.loadWorld(false);
            }
            else {
                return;
            }
        });    
    },
    sweetCookie: null,
};
var click = 0;
const FPS_TRACKER = {

}
function doubleClickDetect() {
    click++;
    if (click == 2) {
        click = 0;
        return true;
    }
    else {
        setTimeout(function () {
            click = 0;
        }, 300);
    }
}
game.properties = {
    fillStyle1: "red",
    fillstyle2: "black",
    duration: 0,
    // Context 1 is for objects
    context: document.getElementById("game_canvas").getContext("2d"),
    // Context 2 is for players
    context2: document.getElementById("game_canvas2").getContext("2d"),
    // Context 3 is for daqloon
    context3: document.getElementById("game_canvas3").getContext("2d"),
    // Context 4 is for objects
    context4: document.getElementById("game_canvas4").getContext("2d"),
    // Context for HUD
    textContext: document.getElementById("text_canvas").getContext("2d"),
    canvasHeight: document.getElementById("game_canvas").height,
    rootCanvas: document.getElementById("game_canvas"),
    requestId: null,
    nagivateNext: false,
    pauseID: null,
    timestamp: 0,
    xbase: 320,
    ybase: 200,
    currentMap: null,
    gameState: "loading",
    // charX and charY where the character is drawn on canvas (middle);
    /*charX: 320,
    charY: 200,*/
    charX: null,
    charY: null,
    // xcamMove/ycamMove is the variables that holds how much the picture is moved. It is - 320 because the player is drawn is the middle on
    // x axis and - 200 on the y axis.
    xcamMove: null,
    ycamMove: null,
    // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
    // 1 is normal then the picture will be painted in 1024 width and height.
    scale: 1.5,
    device: "pc",
    building: "none",
    inBuilding: false,
    checkingPerson: "none",
};
game.setGameState = function (state) {
    if (['playing', 'conversation', 'loading', 'help', 'map', 'pause'].indexOf(state) === -1) {
        alert("Unkown state found: " + state);
        return false;
    }
    game.properties.gameState = state;
}
game.loadWorld = function (parameters = false) {
    game.setGameState('loading');
    window.cancelAnimationFrame(game.properties.requestId);
    loadingCanvas.set('close');
    let data;
    if (parameters !== false) {
        data = "model=worldLoader" + "&method=changeMap" + "&newMap=" + JSON.stringify(parameters.newMap);
    }
    else {
        data = "model=worldLoader" + "&method=loadWorld";
    }
    ajaxG(data, function (response) {
        let responseText = response[1].data;
        if (response[0] != false) {
            game.properties.currentMap = responseText.currentMap;
            if (responseText.changedLocation) {
                document.title = jsUcWords(responseText.changedLocation.replace("-", " "));
            }
            for (let x = 0; x < responseText.events.length; x++) {
                eventHandler.events.push(responseText.events[x]);
            }
            gamePieces.objects = responseText.mapData['objects'];
            function findStartPoint(object) {
                return (object.type === "start_point");
            }
            function removeStartPoint(object) {
                return (object.type !== "start_point");
            }
            let startPoints = gamePieces.objects.filter(findStartPoint);
            let startPoint = getRndInteger(0, startPoints.length);
            gamePieces.objects = gamePieces.objects.filter(removeStartPoint);
            if (parameters.newxBase) {
                // Legge til xbase i JSON map filene
                game.properties.xbase = parameters.newxBase;
            }
            else if (startPoints.length > 0) {
                game.properties.xbase = startPoints[0].x;
            }
            else {
                game.properties.xbase = 800;
            }
            if (parameters.newyBase) {
                // Legge til ybase i JSON map filene
                game.properties.ybase = parameters.newyBase;
            }
            else if (startPoints.length > 0) {
                game.properties.ybase = startPoints[0].y;
            }
            else {
                game.properties.ybase = 1000;
            }
            game.properties.xcamMove = game.properties.xbase - game.properties.charX;
            game.properties.ycamMove = game.properties.ybase - game.properties.charY;
            gamePieces.player.xMovement = 0;
            gamePieces.player.yMovement = 0;
            for (var i = 0; i < gamePieces.objects.length; i++) {
                if (gamePieces.objects[i].src != undefined && gamePieces.objects[i].src.length > 1) {
                    if (gamePieces.objects[i].type === 'character') {
                        gamePieces.objects[i].width = 38;
                        gamePieces.objects[i].height = 38;
                        gamePieces.objects[i].x -= 6;
                        gamePieces.objects[i].y -= 6;
                    }
                    gamePieces.objects[i].img = new Image();
                    if (gamePieces.objects[i].src.indexOf('.png') == -1) gamePieces.objects[i].src += '.png';
                    gamePieces.objects[i].img.src = "public/images/" + gamePieces.objects[i].src;


                }
                gamePieces.objects[i].width *= viewport.scale;
                gamePieces.objects[i].height *= viewport.scale;
                gamePieces.objects[i].drawX = Math.round(gamePieces.objects[i].x - game.properties.xcamMove);
                gamePieces.objects[i].drawY = Math.round(gamePieces.objects[i].y - game.properties.ycamMove);
                if (gamePieces.objects[i].type == "building") {
                    gamePieces.buildings.push(gamePieces.objects[i]);
                }
                else if (gamePieces.objects[i].type == "character") {
                    gamePieces.characters.push(gamePieces.objects[i]);
                }
            }
            gamePieces.objects.sort((a, b) => { return a.diameterDown - b.diameterDown; });
        }
        else {
            viewport.worldImage.src = false;
            location.reload();
            return;
        }
        gamePieces.player.load();
        if (typeof (responseText.mapData['daqloon_fighting_areas']) !== "undefined") {
            gamePieces.daqloon_fighting_area = responseText.mapData['daqloon_fighting_areas'][0];
            checkDaqloon(gamePieces.daqloon_fighting_area.daqloon_amount);
            gamePieces.player.attackedBy = getNearestDaqloon();
        }
        else {
            gamePieces.daqloon_fighting_area = [];
            gamePieces.player.attackedBy = false;
            gamePieces.daqloon = [];
            document.getElementById("HUD_hunted_locater").innerHTML = "";
        }
        viewport.worldImage.src = "public/images/" + game.properties.currentMap + ".png";
        worldMap = new Image(3200, 3200);
        worldMap.src = "public/images/" + game.properties.currentMap + "m.png";
        viewport.worldImage.onload = function () {
            document.getElementById("local_img").src = worldMap.src;
            gamePieces.player.x = game.properties.xbase;
            gamePieces.player.y = game.properties.ybase;
            gamePieces.player.diameterUp = gamePieces.player.y + 20;
            gamePieces.player.diameteRight = gamePieces.player.x + gamePieces.player.width;
            gamePieces.player.diameterDown = gamePieces.player.y + gamePieces.player.height;
            gamePieces.player.diameterLeft = gamePieces.player.x;
            map.loadLocalMapTags();
            map.load();
            // If newMap is false, then the loading is first.
            if(parameters.method !== "changeMap") {
                gamePieces.player.character.src = "public/images/character1.png";
                gamePieces.player.characterAttack.src = "public/images/character attack2.png";
                gamePieces.player.character.onload = function () {
                    game.startGame();
                    loadingCanvas.set('open')
                    if (game.properties.currentMap == "9.9") {
                        tutorial.startTutorial();
                        gamePieces.player.setHuntedStatus(false);
                    }
                };
            }
            else {
                setTimeout(() => {
                    loadingCanvas.set('open')
                    game.startGame();
                }, 4000);
            }
        };
    });
};
game.loadGame = function () {
    controls.checkDeviceType();
    // getHunger();
    itemPrices.get();
    spritesContainer.loadDefaultSprites();
    game.loadWorld();
    CookieTicket.checkCookieTicket('checkMeOut');
};
game.startGame = function () {
    game.properties.requestId = null;
    gamePieces.player.newPos();
    viewport.draw();
    viewport.drawEdge();
    viewport.checkViewportGamePieces(true);
    gamePieces.drawStaticPieces();
    // Resume game and start the pause timer
    pauseManager.resumeGame(true);
};
var draw = false;
document.getElementById("draw_checkbox").addEventListener("click", () => {
    draw = document.getElementById("draw_checkbox").checked;
});
game.update = function (timestamp) {
    game.properties.delta = ((timestamp - game.properties.timestamp) / 1000) / viewport.zoom;
    // Calculate the number of seconds passed since the last frame
    secondsPassed = (timestamp - game.properties.timestamp) / 1000;
    game.properties.timestamp = timestamp;
    // Calculate fps
    if(!FPS_TRACKER[Math.round(1 / secondsPassed)]) {
        FPS_TRACKER[Math.round(1 / secondsPassed)] = 1;
    } else {
        FPS_TRACKER[Math.round(1 / secondsPassed)]++;
    }

    if (game.properties.delta > 0.08) {
        game.properties.delta = Math.round(0.16 / viewport.zoom) * 2;
    }
    game.properties.timestamp = timestamp;
    if (game.properties.gameState !== 'playing') {
        return false;
    }
        gamePieces.player.speedX = 0;
        gamePieces.player.speedY = 0;
        if (controls.playerLeft === true) {
            gamePieces.player.speedX = - gamePieces.player.speed;
        }
        if (controls.playerRight === true) {
            gamePieces.player.speedX = gamePieces.player.speed;
        }
        if (controls.playerUp === true) {
            gamePieces.player.speedY = - gamePieces.player.speed;
        }
        if (controls.playerDown === true) {
            gamePieces.player.speedY = gamePieces.player.speed;
        }
        game.properties.context3.clearRect(0, 0, game.properties.canvasWidth, game.properties.canvasHeight);
        for (let i = 0; i < gamePieces.items.length; i++) {
            gamePieces.items[i].draw();
        }
        if ((gamePieces.player.speedX != 0 || gamePieces.player.speedY != 0) && game.properties.inBuilding == false &&
            conversation.active === false) {
            eventHandler.checkEvent();
            viewport.checkViewportGamePieces();
            collisionCheck();
            gamePieces.player.newPos();
        }
        else if (gamePieces.player.animationEnd != true) {
            gamePieces.player.newPos(false);
        }
        for (var i = 0; i < gamePieces.daqloon.length; i++) {
            if (gamePieces.daqloon[i].dead === false && gamePieces.daqloon[i].spawn === true ||
                gamePieces.daqloon[i].spawn === false) {
                    gamePieces.daqloon[i].draw();
                }
            }
            game.properties.duration++;
    viewport.draw();
    viewport.drawEdge();
    gamePieces.drawStaticPieces();
    game.properties.requestId = window.requestAnimationFrame(game.update);
};
game.getNextMap = function () {
    let newX = 0;
    let newY = 0;
    let newxBase = gamePieces.player.xpos;
    let newyBase = gamePieces.player.ypos;
    let match = false;
    if (gamePieces.player.travel) {
        return false;
    }
    else {
        gamePieces.player.travel = false;
    }
    if (gamePieces.player.diameterDown > 3170 && gamePieces.player.direction.indexOf("down") !== -1) {
        newY += 1;
        newyBase = 1;
        match = true;
    }
    else if (gamePieces.player.diameterUp < 8 && gamePieces.player.direction.indexOf("up") !== -1) {
        newY -= 1;
        newyBase = 3158;
        match = true;
    }
    if (gamePieces.player.xpos > 3170 && gamePieces.player.direction.indexOf("right") !== -1) {
        newX += 1;
        newxBase = 1;
        match = true;
    }
    else if (gamePieces.player.diameterLeft < 8 && gamePieces.player.direction.indexOf("left") !== -1) {
        newX -= 1;
        newxBase = 3158;
        match = true;
    }
    if (match !== true) {
        return false;
    } else {
        game.loadWorld({'newxBase': newxBase, 
                        'newyBase': newyBase, 
                        'method': "changeMap", 
                        'newMap': { "new_x": newX, "new_y": newY }});
    }
};
function renderPlayer(x, y) {
    player = gamePieces.player;
    player.speedX = x;
    player.speedY = y;
    if (game.controls.left == true) { player.speedX = - player.speed; }
    if (game.controls.right == true) { player.speedX = player.speed; }
    if (game.controls.up == true) { player.speedY = - player.speed; }
    if (game.controls.down == true) { player.speedY = player.speed; }
    if (player.speedX != 0 || player.speedY != 0) {
        checkCollision();
        viewport.draw();
        viewport.drawEdge();
        player.newPos();
        game.updateGamePiece(game.properties.xbase, game.properties.ybase);
    }
}
function getObjectPoints(object) {
    if (object.type === "triangle") {
        let slope = object.width / object.height;
        object.slope = slope;
    }
    checkCollision(object);
}
function checkCollision(object) {
    let objectPositionX;
    let objectPositionY;
    for (var i = 0; i < object.width; i++) {
        objectPositionX = objectPositionX * (i * object.slope);
        objectPositionY = objectPositionY * (i * object.slope);
        if (player.x < object.x) {
            if (objectPositionX === player.diameterRight && (player.diameterDown == objectPositionY ||
                player.diameterUp == objectPositionY)) {
                console.log("player.right = blocked");
            }
        }
        else {

        }
        if (player.y < object.y) {
            if (objectPositionY === player.diameterDown && (player.diameteRight == objectPositionX ||
                player.diameterLeft == objectPositionX)) {
                console.log("player.left = blocked");
            }
        }
        else {

        }
    }
}
window.addEventListener("load", UISetup);