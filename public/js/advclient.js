scriptLoader.loadScript([
    'inputHandler', 'help', 'collision', 'gameEventHandler', 'map', 'canvasText', 'conversation',
    'controls', 'spritesContainer', 'testScripts', 'pause', 'inventory', 'tutorial', 'hunger', 'travel'], 'client');

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
const game = {
    properties: {
    HUD: HUD,
    duration: 0,
    requestId: null,
    pauseID: null,
    timestamp: 0,
    xbase: 320,
    ybase: 200,
    currentMap: null,
    gameState: "loading",
    // xcamMove/ycamMove is the variables that holds how much the picture is moved. It is - 320 because the player is drawn is the middle on
    // x axis and - 200 on the y axis.
    xcamMove: null,
    ycamMove: null,
    // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
    // 1 is normal then the picture will be painted in 1024 width and height.
    device: "pc",
    building: "none",
    inBuilding: false,
    checkingPerson: "none",
},
setGameState(state) {
    if (['playing', 'conversation', 'loading', 'help', 'map', 'pause'].indexOf(state) === -1) {
        return false;
    }
    game.properties.gameState = state;
},
loadWorld(parameters = false) {
    game.setGameState('loading');
    window.cancelAnimationFrame(game.properties.requestId);
    if(loadingCanvas.opacity !== 1) loadingCanvas.loadingAnimationTracker.start('close');
    let data;
    if (parameters !== false) {
        data = "model=worldLoader" + "&method=changeMap" + "&newMap=" + JSON.stringify(parameters.newMap);
    }
    else {
        data = "model=worldLoader&method=loadWorld";
    }
    ajaxG(data, function (response) {
        let responseText = response[1].data;
        if (response[0] == false) {
            viewport.worldImage.src = false;
            location.reload();
            return;
        }
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
        let worldMapSrc = "public/images/" + game.properties.currentMap + ".png";
        viewport.adjustViewport(game.properties.xbase, game.properties.ybase, worldMapSrc);
        viewport.checkViewportGamePieces(true);
        gamePieces.loadAssets(game.properties.xbase, game.properties.ybase, responseText.mapData);
        map.load(game.properties.currentMap);
        if(parameters.method !== "changeMap") {
            loadingCanvas.loadingAnimationTracker.start('open');
            game.startGame();
            if (game.properties.currentMap == "9.9") {
                tutorial.startTutorial();
            }
        }
        else {
            setTimeout(() => {
                loadingCanvas.loadingAnimationTracker.start('open');
                game.startGame();
            }, 3000);
        }
    });
},
getNextWorld() {
    let newX = 0;
    let newY = 0;
    let newxBase = gamePieces.player.xpos;
    let newyBase = gamePieces.player.ypos;
    let match = false;
    if (gamePieces.player.diameterDown > 3170 && gamePieces.player.direction.indexOf("down") !== -1) {
        newY += 1;
        newyBase = 1;
        match = true;
    }
    else if (gamePieces.player.diameterUp < 10 && gamePieces.player.direction.indexOf("up") !== -1) {
        newY -= 1;
        newyBase = 3158;
        match = true;
    }
    if (gamePieces.player.xpos > 3170 && gamePieces.player.direction.indexOf("right") !== -1) {
        newX += 1;
        newxBase = 1;
        match = true;
    }
    else if (gamePieces.player.diameterLeft < 10 && gamePieces.player.direction.indexOf("left") !== -1) {
        newX -= 1;
        newxBase = 3158;
        match = true;
    }
    if (match !== true) {
        return false;
    } else {
        gamePieces.player.travel = true;
        this.loadWorld({'newxBase': newxBase, 
                        'newyBase': newyBase, 
                        'method': "changeMap", 
                        'newMap': { "new_x": newX, "new_y": newY }});
    }
},
setup() {
    gamePieces.player.setup();
    spritesContainer.loadDefaultSprites();
    setTimeout(() => {
        document.getElementById("client-container").style.opacity = 1;
        document.getElementById("client-loading-container").style.display = "none";
        this.loadGame();
    }, 5000);
},
loadGame() {
    this.loadWorld();
    viewport.setup([
        document.getElementById("game_canvas"),
        document.getElementById("game_canvas2"),
        document.getElementById("game_canvas3"),
        document.getElementById("game_canvas4"),
        document.getElementById("text_canvas")], game.properties.xbase, game.properties.ybase);
    HUD.setup(viewport.width, viewport.height, 
        document.getElementById("game_canvas").offsetTop, 
        document.getElementById("game_canvas").offsetLeft);
    controls.checkDeviceType();
    // getHunger();
    itemPrices.get();
    CookieTicket.checkCookieTicket('checkMeOut');
},
startGame() {
    game.properties.requestId = null;
    // GamePieces is called here so that player position is set before viewport.checkViewportGamePieces is called
    gamePieces.player.newPos();
    map.locatePlayerMarker()
    viewport.init();
    gamePieces.init();
    pauseManager.resumeGame(true);
},
update(timestamp) {
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
    viewport.resetSpriteLayer();
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
    viewport.drawBackground(gamePieces.player.xMovement, gamePieces.player.yMovement);
    gamePieces.drawStaticPieces();
    game.properties.duration++;
    if(gamePieces.player.checkPosition()) game.getNextWorld();
    game.properties.requestId = window.requestAnimationFrame(game.update);
},
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
var draw = false;
document.getElementById("draw_checkbox").addEventListener("click", () => {
    draw = document.getElementById("draw_checkbox").checked;
});
window.addEventListener("load", () => game.setup());