import { loadingCanvas } from './clientScripts/canvasText';
import { ClientOverlayInterface } from './clientScripts/clientOverlayInterface';
import { collisionCheck } from './clientScripts/collision';
import { controls } from './clientScripts/controls';
import { eventHandler } from './clientScripts/gameEventHandler';
import { GamePieces } from './clientScripts/gamePieces';
import { pauseManager } from './clientScripts/pause';
import viewport from './clientScripts/viewport';
import { CustomFetchApi } from './CustomFetchApi';
import type { GameProperties, loadWorldParameters } from './types/Advclient';
import { formatLocationName } from './utilities/formatters';
import { setUpTabList } from './utilities/tabs';
import type { GetWorldResponse } from './types/Responses/WorldLoaderResponse';
import { initErrorHandler, reportCatchError } from './base/ErrorHandler';
import { useConversationStore } from './ui/stores/ConversationStore';
import { gameEventBus } from './gameEventsBus';

export type AdvClientEvents = {
  CHANGED_LOCATION: { locationName: string };
};

// eslint-disable-next-line @typescript-eslint/no-extraneous-class
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

  public static worldData: GetWorldResponse['data'];

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
      ['playing', 'conversation', 'loading', 'help', 'map', 'pause'].indexOf(
        state,
      ) === -1
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
      .catch((error: unknown) => {
        reportCatchError(error);
      });
  }

  public static setWorldData(data: GetWorldResponse['data']) {
    this.worldData = data;
  }

  public static setWorld(parameters: loadWorldParameters) {
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

    CustomFetchApi.post<GetWorldResponse>('/worldloader/change', data)
      .then(response => {
        this.loadWorld(response.data, parameters);

        viewport.adjustViewport(Game.properties.xbase, Game.properties.ybase);
        GamePieces.loadAssets(
          Game.properties.xbase,
          Game.properties.ybase,
          this.worldData.map_data,
        );
        setTimeout(() => {
          void loadingCanvas.loadingAnimationTracker.start('open');
          Game.startGame();
        }, 3000);
      })
      .catch((error: unknown) => {
        reportCatchError(error);
      });
  }

  private static pauseWorld() {
    window.cancelAnimationFrame(Game.properties.requestId);
    Game.setGameState('loading');

    GamePieces.player.travel = false;
    if (loadingCanvas.opacity !== 1)
      void loadingCanvas.loadingAnimationTracker.start('close');

    GamePieces.reset();
  }

  public static loadWorld(
    response: GetWorldResponse,
    parameters?: loadWorldParameters,
  ) {
    const data = response.data;
    Game.properties.currentMap = data.current_map;
    if (data.changed_location) {
      document.title = formatLocationName(data.changed_location);
      gameEventBus.emit('CHANGED_LOCATION', {
        locationName: data.changed_location,
      });
    }
    const findStartPoint = (object: { type: string }) =>
      object.type === 'start_point';
    const removeStartPoint = (object: { type: string }) =>
      object.type !== 'start_point';
    const startPoints = data.map_data.objects.filter(findStartPoint);
    response.data.map_data.objects =
      response.data.map_data.objects.filter(removeStartPoint);

    if (parameters?.method === 'nextMap') {
      Game.properties.xbase = parameters.newxBase;
    } else if (startPoints.length > 0) {
      Game.properties.xbase = startPoints[0].x;
    }

    if (parameters?.method === 'nextMap') {
      Game.properties.ybase = parameters.newyBase;
    } else if (startPoints.length > 0) {
      Game.properties.ybase = startPoints[0].y;
    }

    viewport.setImageWorldSrc();
    this.setWorldData(response.data);
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

    if (!match) {
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
    setUpTabList();

    ClientOverlayInterface.setup();

    viewport.setup({
      background: document.getElementById('game_canvas') as HTMLCanvasElement,
      player: document.getElementById('game_canvas2') as HTMLCanvasElement,
      frontObjects: document.getElementById(
        'game_canvas4',
      ) as HTMLCanvasElement,
      sprite: document.getElementById('game_canvas3') as HTMLCanvasElement,
      text: document.getElementById('text_canvas') as HTMLCanvasElement,
      hud: document.getElementById('hud_canvas') as HTMLCanvasElement,
    });
    viewport.adjustViewport(Game.properties.xbase, Game.properties.ybase);

    controls.setup();

    GamePieces.loadAssets(
      Game.properties.xbase,
      Game.properties.ybase,
      this.worldData.map_data,
    );

    loadingCanvas.loadingScreen();
    setTimeout(() => {
      void loadingCanvas.loadingAnimationTracker.start('open');
      Game.startGame();
    }, 3000);

    setTimeout(() => {
      const clientContainer = document.getElementById('client-container');
      if (clientContainer) {
        clientContainer.style.opacity = '1';
      }
    }, 500);
  }

  private static startGame() {
    Game.properties.requestId = 0;
    GamePieces.player.draw();
    GamePieces.init();
    pauseManager.resumeGame(true);
  }

  public static update = (timestamp: number) => {
    Game.properties.delta =
      (timestamp - Game.properties.timestamp) / 1000 / viewport.zoom;
    if (Game.properties.delta > 0.08) {
      Game.properties.delta = Math.round(0.16 / viewport.zoom) * 2;
    }
    Game.properties.timestamp = timestamp;
    if (Game.properties.gameState !== 'playing') {
      return false;
    }

    if (controls.playerLeft) {
      GamePieces.player.speedX = -GamePieces.player.speed;
    } else if (controls.playerRight) {
      GamePieces.player.speedX = GamePieces.player.speed;
    } else {
      GamePieces.player.speedX = 0;
    }
    if (controls.playerUp) {
      GamePieces.player.speedY = -GamePieces.player.speed;
    } else if (controls.playerDown) {
      GamePieces.player.speedY = GamePieces.player.speed;
    } else {
      GamePieces.player.speedY = 0;
    }
    viewport.resetSpriteLayer();
    if (
      // (GamePieces.player.speedX != 0 || GamePieces.player.speedY != 0) &&
      !Game.properties.inBuilding &&
      !useConversationStore().isActive
    ) {
      eventHandler.checkEvent();
      GamePieces.checkViewportGamePieces();
      collisionCheck(GamePieces.player, false);
      GamePieces.player.newPos();
    } else if (!GamePieces.player.animationEnd) {
      GamePieces.player.newPos(false);
    }

    GamePieces.player.xTracker = GamePieces.player.xMovement +=
      Math.round(GamePieces.player.movementSpeed * Game.properties.delta) *
      GamePieces.player.speedX;
    GamePieces.player.yTracker = GamePieces.player.yMovement +=
      Math.round(GamePieces.player.movementSpeed * Game.properties.delta) *
      GamePieces.player.speedY;

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
  };
}
