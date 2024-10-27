import { ajaxP } from './ajax';
import { loadingCanvas } from './clientScripts/canvasText';
import { ClientOverlayInterface } from './clientScripts/clientOverlayInterface';
import { collisionCheck } from './clientScripts/collision';
import { controls } from './clientScripts/controls';
import { conversation } from './clientScripts/conversation';
import { eventHandler } from './clientScripts/gameEventHandler';
import { GamePieces } from './clientScripts/gamePieces';
import { HUD } from './clientScripts/HUD';
import { itemPrices } from './clientScripts/inventory';
import { Map } from './clientScripts/map';
import { pauseManager } from './clientScripts/pause';
import { tutorial } from './clientScripts/tutorial';
import viewport from './clientScripts/viewport';
import { CustomFetchApi } from './CustomFetchApi';
import { GameProperties, loadWorldParameters } from './types/Advclient';
import { jsUcWords } from './utilities/uppercase';
import { setUpTabList } from './utilities/tabs';
import { AssetPaths } from './clientScripts/ImagePath';
import { GetWorldResponse } from './types/Responses/WorldLoaderResponse';
import { initErrorHandler, reportCatchError } from './base/ErrorHandler';

const CookieTicket = {
    checkCookieTicket(cookieNoob = 'getOut') {
        const today = new Date();
        let cookieTicket;
        if (CookieTicket.sweetCookie === null) {
            cookieTicket = today.getMonth() + today.getDate() + '|';
            for (let i = 0; i < 10; i++) {
                cookieTicket += Math.floor(Math.random() * (10 - 1) + 1);
            }
            CookieTicket.sweetCookie = cookieTicket;
        } else {
            cookieTicket = CookieTicket.sweetCookie;
        }
        const data =
            'model=cookieMaker' +
            '&method=yummyCookies' +
            '&cookieTicket=' +
            cookieTicket +
            '&cookieNoob=' +
            cookieNoob;
        ajaxP(data, function (response) {
            console.log(response);
            const responseText = response[1].status;
            if (responseText.status === 'false') {
                // Session check return false, go to logout
                alert(
                    'A newer session is ongoing. You are being logged out from this session',
                );
                setTimeout(() => {
                    location.href = '/logout';
                }, 5000);
            } else {
                return;
            }
        });
    },
    disposeGarbage(event) {
        const ego = event.target.innerText;
        if (ego === '') {
            this.checkCookieTicket();
        } else {
            this.burntCookie();
        }
    },
    burntCookie() {
        const data =
            'model=cookieMaker' +
            '&method=delicCookies' +
            '&cookieTicket=' +
            CookieTicket.sweetCookie;
        ajaxP(data, function (response) {
            console.log(response);
            if (response[1] == false) {
                window.cancelAnimationFrame(Game.properties.requestId);
                // Game.loadWorld({});
            } else {
                return;
            }
        });
    },
    sweetCookie: null,
};

export class Game {
    public static properties: GameProperties = {
        duration: 0,
        requestId: 0,
        pauseID: null,
        timestamp: 0,
        xbase: 320,
        ybase: 200,
        currentMap: '0.0',
        gameState: 'loading',
        // Scale is a variable which compensates for the canvas being zoomed in so that objects drawn on canvas will follow the background.
        // 1 is normal then the picture will be painted in 1024 width and height.
        device: 'pc',
        building: 'none',
        inBuilding: false,
        checkingPerson: 'none',
        delta: 0,
    };

    public static getProperty(val: keyof GameProperties) {
        return this.properties[val];
    }

    /**
     * Threshold 60 will be around 1 second
     * @returns boolean
     */
    public static isGameDuration(threshold: number) {
        if (this.properties.duration % threshold === 0) {
            return true;
        } else {
            return false;
        }
    }

    public static setGameState(state: string) {
        // TODO: Fix enum here
        if (
            [
                'playing',
                'conversation',
                'loading',
                'help',
                'map',
                'pause',
            ].indexOf(state) === -1
        ) {
            return false;
        }
        this.properties.gameState = state;
    }

    public static async getWorld() {
        this.pauseWorld();
        await CustomFetchApi.get<GetWorldResponse>('/worldloader')
            .then(response => {
                this.loadWorld(response.data);
            })
            .catch(error => reportCatchError(error));
    }

    public static async setWorld(parameters: loadWorldParameters) {
        this.pauseWorld();

        let data;
        if (parameters.method === 'nextMap') {
            data = {
                method: parameters.method,
                new_map: parameters.newMap,
            };
        } else if (parameters.method === 'travel') {
            data = {
                method: parameters.method,
                new_destination: parameters.newDestination,
            };
        } else {
            data = {
                method: parameters.method,
            };
        }

        await CustomFetchApi.post<GetWorldResponse>('/worldloader/change', data)
            .then(async response => {
                await this.loadWorld(response.data, parameters);
            })
            .catch(error => reportCatchError(error));
    }

    private static pauseWorld() {
        window.cancelAnimationFrame(Game.properties.requestId);
        Game.setGameState('loading');

        GamePieces.player.travel = false;
        if (loadingCanvas.opacity !== 1)
            loadingCanvas.loadingAnimationTracker.start('close');

        GamePieces.reset();
    }

    public static async loadWorld(
        response: GetWorldResponse,
        parameters?: loadWorldParameters,
    ) {
        const data = response.data;
        Game.properties.currentMap = data.current_map;
        if (data.changed_location) {
            document.title = jsUcWords(data.changed_location.replace('-', ' '));
        }
        // for (let x = 0; x < data.events.length; x++) {
        //     eventHandler.events.push(data.events[x]);
        // }
        function findStartPoint(object) {
            return object.type === 'start_point';
        }
        function removeStartPoint(object) {
            return object.type !== 'start_point';
        }
        const startPoints = data.map_data.objects.filter(findStartPoint);
        GamePieces.objects = GamePieces.objects.filter(removeStartPoint);

        if (parameters?.method === 'nextMap' && parameters.newxBase != null) {
            // Legge til xbase i JSON map filene
            Game.properties.xbase = parameters.newxBase;
        } else if (startPoints.length > 0) {
            Game.properties.xbase = startPoints[0].x;
        }

        if (parameters?.method === 'nextMap' && parameters.newxBase !== null) {
            // Legge til ybase i JSON map filene
            Game.properties.ybase = parameters.newyBase;
        } else if (startPoints.length > 0) {
            Game.properties.ybase = startPoints[0].y;
        }

        const worldMapSrc = AssetPaths.getImagePath(
            Game.properties.currentMap + '.png',
        );
        viewport.adjustViewport(
            Game.properties.xbase,
            Game.properties.ybase,
            worldMapSrc,
        );

        GamePieces.loadAssets(
            Game.properties.xbase,
            Game.properties.ybase,
            data.map_data,
        );
        viewport.checkViewportGamePieces(true);

        Map.load(Game.properties.currentMap);

        setTimeout(() => {
            loadingCanvas.loadingAnimationTracker.start('open');
            Game.startGame();
            // if (
            //     parameters.method !== 'changeMap' &&
            //     Game.properties.currentMap == '9.9'
            // ) {
            //     if (Game.properties.currentMap == '9.9') {
            //         tutorial.startTutorial();
            //     }
            // }
        }, 3000);
    }

    private static getNextWorld() {
        let newX = 0;
        let newY = 0;
        let newxBase = GamePieces.player.xpos;
        let newyBase = GamePieces.player.ypos;
        let match = false;

        if (
            GamePieces.player.diameterDown > 3170 &&
            GamePieces.player.direction.indexOf('down') !== -1
        ) {
            newY += 1;
            newyBase = 1;
            match = true;
        } else if (
            GamePieces.player.diameterUp < 50 &&
            GamePieces.player.direction.indexOf('up') !== -1
        ) {
            newY -= 1;
            newyBase = 3158;
            match = true;
        }
        if (
            GamePieces.player.xpos > 3170 &&
            GamePieces.player.direction.indexOf('right') !== -1
        ) {
            newX += 1;
            newxBase = 1;
            match = true;
        } else if (
            GamePieces.player.diameterLeft < 50 &&
            GamePieces.player.direction.indexOf('left') !== -1
        ) {
            newX -= 1;
            newxBase = 3158;
            match = true;
        }

        if (match !== true) {
            return false;
        } else {
            GamePieces.player.travel = true;

            this.setWorld({
                method: 'nextMap',
                newMap: { newX: newX, newY: newY },
                newxBase,
                newyBase,
            });
        }
    }

    public static setup() {
        initErrorHandler();
        conversation.setup();
        setUpTabList();

        ClientOverlayInterface.setup();

        viewport.setup({
            background: document.getElementById(
                'game_canvas',
            ) as HTMLCanvasElement,
            player: document.getElementById(
                'game_canvas2',
            ) as HTMLCanvasElement,
            frontObjects: document.getElementById(
                'game_canvas4',
            ) as HTMLCanvasElement,
            sprite: document.getElementById(
                'game_canvas3',
            ) as HTMLCanvasElement,
            text: document.getElementById('text_canvas') as HTMLCanvasElement,
            hud: document.getElementById('hud_canvas') as HTMLCanvasElement,
        });
        // Initial loading screen
        loadingCanvas.loadingScreen();

        this.loadGame();
        setTimeout(() => {
            document.getElementById('client-container').style.opacity = '' + 1;
        }, 500);
    }

    private static loadGame() {
        this.getWorld();
        HUD.setup(viewport.width, viewport.height, viewport.top, viewport.left);
        controls.checkDeviceType();
        itemPrices.get();
        // CookieTicket.checkCookieTicket("checkMeOut");
    }

    private static startGame() {
        Game.properties.requestId = null;
        GamePieces.player.draw();
        GamePieces.init();
        pauseManager.resumeGame(true);
    }

    public static update(timestamp) {
        Game.properties.delta =
            (timestamp - Game.properties.timestamp) / 1000 / viewport.zoom;
        if (Game.properties.delta > 0.08) {
            Game.properties.delta = Math.round(0.16 / viewport.zoom) * 2;
        }
        Game.properties.timestamp = timestamp;
        // Calculate the number of seconds passed since the last frame
        // let secondsPassed = (timestamp - Game.properties.timestamp) / 1000;
        // Calculate fps
        // if (!FPS_TRACKER[Math.round(1 / secondsPassed)]) {
        //     FPS_TRACKER[Math.round(1 / secondsPassed)] = 1;
        // } else {
        //     FPS_TRACKER[Math.round(1 / secondsPassed)]++;
        // }
        // console.log("FPS: " + Math.round(1 / secondsPassed));

        if (Game.properties.gameState !== 'playing') {
            return false;
        }

        if (controls.playerLeft === true) {
            GamePieces.player.speedX = -GamePieces.player.speed;
        } else if (controls.playerRight === true) {
            GamePieces.player.speedX = GamePieces.player.speed;
        } else {
            GamePieces.player.speedX = 0;
        }
        if (controls.playerUp === true) {
            GamePieces.player.speedY = -GamePieces.player.speed;
        } else if (controls.playerDown === true) {
            GamePieces.player.speedY = GamePieces.player.speed;
        } else {
            GamePieces.player.speedY = 0;
        }
        viewport.resetSpriteLayer();
        if (
            // (GamePieces.player.speedX != 0 || GamePieces.player.speedY != 0) &&
            Game.properties.inBuilding == false &&
            conversation.isActive === false
        ) {
            eventHandler.checkEvent();
            viewport.checkViewportGamePieces();
            collisionCheck(GamePieces.player, false);
            GamePieces.player.newPos();
        } else if (GamePieces.player.animationEnd != true) {
            GamePieces.player.newPos(false);
        }

        GamePieces.player.xTracker = GamePieces.player.xMovement +=
            Math.round(
                GamePieces.player.movementSpeed * Game.properties.delta,
            ) * GamePieces.player.speedX;
        GamePieces.player.yTracker = GamePieces.player.yMovement +=
            Math.round(
                GamePieces.player.movementSpeed * Game.properties.delta,
            ) * GamePieces.player.speedY;

        viewport.drawBackground(
            GamePieces.player.xMovement,
            GamePieces.player.yMovement,
        );

        GamePieces.drawStaticPieces();
        GamePieces.drawDaqloons();

        if (GamePieces.player.checkPosition()) {
            Game.getNextWorld();
        }
        Game.properties.duration++;
        Game.properties.requestId = window.requestAnimationFrame(Game.update);
    }
}

window.addEventListener('load', () => Game.setup());
