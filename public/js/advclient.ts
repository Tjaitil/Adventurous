import { sidebar } from "./clientScripts/sidebar.js";
import { pauseManager } from "./clientScripts/pause.js";
import { GamePieces } from "./clientScripts/gamePieces.js";
import { Map } from "./clientScripts/map.js";
import { tutorial } from "./clientScripts/tutorial.js";
import { loadingCanvas } from "./clientScripts/canvasText.js";
import { eventHandler } from "./clientScripts/gameEventHandler.js";
import { itemPrices } from "./clientScripts/inventory.js";
import { HUD } from "./clientScripts/HUD.js";
import { controls } from "./clientScripts/controls.js";
import { ajaxP, ajaxG } from "./ajax.js";
import viewport from "./clientScripts/viewport.js";
import { getRandomInteger } from "./utilities/getRandomInteger.js";
import { conversation } from "./clientScripts/conversation.js";
import { collisionCheck } from "./clientScripts/collision.js";
import { jsUcWords } from "./utilities/uppercase.js";
import { clientOverlayInterface } from "./clientScripts/clientOverlayInterface.js";
import { GameProperties, loadWorldParamters } from "./types/Advclient.js";

const CookieTicket = {
    checkCookieTicket(cookieNoob = "getOut") {
        var today = new Date();
        var cookieTicket;
        if (CookieTicket.sweetCookie === null) {
            cookieTicket = today.getMonth() + today.getDate() + "|";
            for (var i = 0; i < 10; i++) {
                cookieTicket += Math.floor(Math.random() * (10 - 1) + 1);
            }
            CookieTicket.sweetCookie = cookieTicket;
        } else {
            cookieTicket = CookieTicket.sweetCookie;
        }
        let data =
            "model=cookieMaker" +
            "&method=yummyCookies" +
            "&cookieTicket=" +
            cookieTicket +
            "&cookieNoob=" +
            cookieNoob;
        ajaxP(data, function (response) {
            console.log(response);
            let responseText = response[1].status;
            if (responseText.status === "false") {
                // Session check return false, go to logout
                alert("A newer session is ongoing. You are being logged out from this session");
                setTimeout(() => {
                    location.href = "/logout";
                }, 5000);
            } else {
                return;
            }
        });
    },
    disposeGarbage(event) {
        let ego = event.target.innerText;
        if (ego === "") {
            this.checkCookieTicket();
        } else {
            this.burntCookie();
        }
    },
    burntCookie() {
        let data = "model=cookieMaker" + "&method=delicCookies" + "&cookieTicket=" + CookieTicket.sweetCookie;
        ajaxP(data, function (response) {
            console.log(response);
            if (response[1] == false) {
                window.cancelAnimationFrame(Game.properties.requestId);
                Game.loadWorld({});
            } else {
                return;
            }
        });
    },
    sweetCookie: null,
};
var click = 0;
const FPS_TRACKER = {};
function doubleClickDetect() {
    click++;
    if (click == 2) {
        click = 0;
        return true;
    } else {
        setTimeout(function () {
            click = 0;
        }, 300);
    }
}

export class Game {
    public static properties: GameProperties = {
        HUD: HUD,
        duration: 0,
        requestId: 0,
        pauseID: null,
        timestamp: 0,
        xbase: 320,
        ybase: 200,
        currentMap: "0.0",
        gameState: "loading",
        // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
        // 1 is normal then the picture will be painted in 1024 width and height.
        device: "pc",
        building: "none",
        inBuilding: false,
        checkingPerson: "none",
        delta: 0,
    }

    public static setGameState(state: string) {
        // TODO: Fix enum here
        if (["playing", "conversation", "loading", "help", "map", "pause"].indexOf(state) === -1) {
            return false;
        }
        Game.properties.gameState = state;
    }

    public static async loadWorld(parameters: loadWorldParamters = {}) {
        window.cancelAnimationFrame(Game.properties.requestId);
        Game.setGameState("loading");
        
        GamePieces.player.travel = false;
        if (loadingCanvas.opacity !== 1) loadingCanvas.loadingAnimationTracker.start("close");
        
        GamePieces.reset();

        // Close curtain effect on naviagtion
        let data;
        if (parameters.method && parameters.newDestination) {
            data = "model=worldLoader" + "&method=changeMap" + "&newDestination=" + parameters.newDestination;
        } else if(parameters.method) {
            data = "model=worldLoader" + "&method=changeMap" + "&newMap=" + JSON.stringify(parameters.newMap);
        } else {
            data = "model=worldLoader&method=loadWorld";
        }
        ajaxG(data, (response) => {
            let responseText = response[1].data;
            if (response[0] == false) {
                viewport.worldImage.src = "";
                location.reload();
                return;
            }
            Game.properties.currentMap = responseText.currentMap;
            if (responseText.changedLocation) {
                document.title = jsUcWords(responseText.changedLocation.replace("-", " "));
            }
            for (let x = 0; x < responseText.events.length; x++) {
                eventHandler.events.push(responseText.events[x]);
            }
            function findStartPoint(object) {
                return object.type === "start_point";
            }
            function removeStartPoint(object) {
                return object.type !== "start_point";
            }
            let startPoints = responseText.mapData.objects.filter(findStartPoint);
            let startPoint = getRandomInteger(0, startPoints.length);
            GamePieces.objects = GamePieces.objects.filter(removeStartPoint);

            if (parameters.newxBase) {
                // Legge til xbase i JSON map filene
                Game.properties.xbase = parameters.newxBase;
            } 
            // else if (startPoints.length > 0) {
            //     Game.properties.xbase = startPoints[0].x;
            // } 
            else {
                Game.properties.xbase = 2500;
            }
            if (parameters.newyBase) {
                // Legge til ybase i JSON map filene
                Game.properties.ybase = parameters.newyBase;
            }
            //  else if (startPoints.length > 0) {
            //     Game.properties.ybase = startPoints[0].y;
            // } 
            else {
                Game.properties.ybase = 2952;
            }

            let worldMapSrc = "public/images/" + Game.properties.currentMap + ".png";
            viewport.adjustViewport(Game.properties.xbase, Game.properties.ybase, worldMapSrc);

            GamePieces.loadAssets(Game.properties.xbase, Game.properties.ybase, responseText.mapData);
            viewport.checkViewportGamePieces(true);
            // viewport.init();

            Map.load(Game.properties.currentMap);

            setTimeout(() => {
                loadingCanvas.loadingAnimationTracker.start("open");
                Game.startGame();
                if (parameters.method !== "changeMap" && Game.properties.currentMap == "9.9") {
                    if (Game.properties.currentMap == "9.9") {
                        tutorial.startTutorial();
                    }
                }
            }, 3000);
        });
    }

    private static getNextWorld() {
        let newX = 0;
        let newY = 0;
        let newxBase = GamePieces.player.xpos;
        let newyBase = GamePieces.player.ypos;
        let match = false;

        if (GamePieces.player.diameterDown > 3170 && GamePieces.player.direction.indexOf("down") !== -1) {
            newY += 1;
            newyBase = 1;
            match = true;
        } else if (GamePieces.player.diameterUp < 50 && GamePieces.player.direction.indexOf("up") !== -1) {
            newY -= 1;
            newyBase = 3158;
            match = true;
        }
        if (GamePieces.player.xpos > 3170 && GamePieces.player.direction.indexOf("right") !== -1) {
            newX += 1;
            newxBase = 1;
            match = true;
        } else if (GamePieces.player.diameterLeft < 50 && GamePieces.player.direction.indexOf("left") !== -1) {
            newX -= 1;
            newxBase = 3158;
            match = true;
        }

        if (match !== true) {
            return false;
        } else {
            GamePieces.player.travel = true;
            this.loadWorld({
                newxBase: newxBase,
                newyBase: newyBase,
                method: "changeMap",
                newMap: { newX, newY },
            });
        }
    }

    public static setup() {
        conversation.setup();

        // Remove this ?
        sidebar.addClickEvent();
        clientOverlayInterface.setup();

        viewport.setup([
            <HTMLCanvasElement>document.getElementById("game_canvas"),
            <HTMLCanvasElement>document.getElementById("game_canvas2"),
            <HTMLCanvasElement>document.getElementById("game_canvas3"),
            <HTMLCanvasElement>document.getElementById("game_canvas4"),
            <HTMLCanvasElement>document.getElementById("text_canvas"),
        ]);
        // Initial loading screen
        loadingCanvas.loadingScreen();

        setTimeout(() => {
            document.getElementById("client-container").style.opacity = "" + 1;
            document.getElementById("client-loading-container").style.display = "none";
            this.loadGame();
        }, 5000);
    }

    private static loadGame() {

        this.loadWorld();
        HUD.setup(viewport.width, viewport.height, viewport.top, viewport.left);
        controls.checkDeviceType();
        // getHunger();
        itemPrices.get();
        // CookieTicket.checkCookieTicket("checkMeOut");
    }

    private static startGame() {
        Game.properties.requestId = null;
        GamePieces.player.draw();
        GamePieces.init();
        pauseManager.resumeGame(true);
    }

    public static update(timestamp, first = false) {
        Game.properties.delta = (timestamp - Game.properties.timestamp) / 1000 / viewport.zoom;

        if (GamePieces.player.travel === true) return false;

        // Calculate the number of seconds passed since the last frame
        let secondsPassed = (timestamp - Game.properties.timestamp) / 1000;
        Game.properties.timestamp = timestamp;
        // Calculate fps
        if (!FPS_TRACKER[Math.round(1 / secondsPassed)]) {
            FPS_TRACKER[Math.round(1 / secondsPassed)] = 1;
        } else {
            FPS_TRACKER[Math.round(1 / secondsPassed)]++;
        }

        if (Game.properties.delta > 0.08) {
            Game.properties.delta = Math.round(0.16 / viewport.zoom) * 2;
        }
        Game.properties.timestamp = timestamp;
        if (Game.properties.gameState !== "playing") {
            return false;
        }
        GamePieces.player.speedX = 0;
        GamePieces.player.speedY = 0;

        if (controls.playerLeft === true) {
            GamePieces.player.speedX = -GamePieces.player.speed;
        }
        if (controls.playerRight === true) {
            GamePieces.player.speedX = GamePieces.player.speed;
        }
        if (controls.playerUp === true) {
            GamePieces.player.speedY = -GamePieces.player.speed;
        }
        if (controls.playerDown === true) {
            GamePieces.player.speedY = GamePieces.player.speed;
        }
        viewport.resetSpriteLayer();
        if (
            (GamePieces.player.speedX != 0 || GamePieces.player.speedY != 0) &&
            Game.properties.inBuilding == false &&
            conversation.active === false
        ) {
            eventHandler.checkEvent();
            viewport.checkViewportGamePieces();
            collisionCheck(GamePieces.player, false);
            GamePieces.player.newPos();
        } else if (GamePieces.player.animationEnd != true) {
            GamePieces.player.newPos(false);
        }

        GamePieces.player.xTracker = GamePieces.player.xMovement +=
            Math.round(GamePieces.player.movementSpeed * Game.properties.delta) * GamePieces.player.speedX;
        GamePieces.player.yTracker = GamePieces.player.yMovement +=
            Math.round(GamePieces.player.movementSpeed * Game.properties.delta) * GamePieces.player.speedY;

        viewport.drawBackground(GamePieces.player.xMovement, GamePieces.player.yMovement);

        GamePieces.drawStaticPieces();
        GamePieces.drawDaqloons();
        
        if (GamePieces.player.checkPosition()) {
            Game.getNextWorld();
        } 
        Game.properties.duration++;
        Game.properties.requestId = window.requestAnimationFrame(Game.update);
    }
};

window.addEventListener("load", () => Game.setup());
