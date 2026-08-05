import { ModuleTester, addModuleTester } from './../devtools/ModuleTester';
import { Character } from './../gamepieces/Character';
import { ClientOverlayInterface } from './clientOverlayInterface';
import { tutorial } from './tutorial';
import { itemTitle } from '../utilities/itemTitle';
import { Game } from '../advclient';
import { GameLogger } from '../utilities/GameLogger';
import type { BuildingName } from '@/types/Building';
import { isVuePage, type VuePage } from '@/types/Building';
import { GamePieces } from './gamePieces';
import { Building } from '../gamepieces/Building';
import { setUpTabList } from '../utilities/tabs';
import travelBureauModule from '../buildingScripts/travelbureau';
import bakeryModule from '../buildingScripts/bakery';
import MineModule from '../buildingScripts/mine';
import CropsModule from '../buildingScripts/crops';
import zinsStoreModule from '../buildingScripts/zinsstore';
import merchantModule from '../buildingScripts/merchant';
import workforceLodgeModule from '../buildingScripts/workforcelodge';
import smithyModule from '../buildingScripts/smithy';
import archeryShopModule from '../buildingScripts/archeryshop';
import { useConversationStore } from '@/ui/stores/ConversationStore';
import { gameEventBus } from '@/gameEventsBus';
import { buildingDataPreloader } from '@/ui/services/buildingDataPreloader';
import { reportError } from '@/ui/errorReporting';

export enum Buildings {
  BAKERY = 'bakery',
  TRAVELBUREAU = 'travelbureau',
  STOCKPILE = 'stockpile',
  MINE = 'mine',
  CROPS = 'crops',
  ZINSSTORE = 'zinsstore',
  MERCHANT = 'merchant',
  WORKFORCELODGE = 'workforcelodge',
  SMITHY = 'smithy',
  ARCHERYSHOP = 'archeryshop',
  ARMORY = 'armory',
}

export interface InputHandlerEvents {
  RENDER_BUILDING:
    | {
        content: string;
        building: Exclude<BuildingName, VuePage>;
      }
    | { building: VuePage }
    | { loading: true };
  HUD_BUILDING_PROMPT_UPDATE: {
    buildingName: string | null;
  };
  HUD_CONVERSATION_PROMPT_UPDATE: {
    characterName: string | null;
  };
}

function shouldSkipImport(building: string) {
  return [
    'stockpile',
    'travelbureau',
    'bakery',
    'mine',
    'crops',
    'zinsstore',
    'merchant',
    'workforcelodge',
    'smithy',
    'archeryshop',
    'armory',
  ].includes(building);
}

interface BuildingAssetsTypes {
  stylesheets?: string[];
  script?: string;
}
type BuildingAssetsRecord = Record<Buildings, BuildingAssetsTypes>;

interface IInputHandler {
  buildingAssetsRecord: BuildingAssetsRecord;
  buildingMatch: undefined | Building;
  buildingMatchUIChanged: boolean;
  checkBuilding(mouseinputX?: number, mouseinputY?: number): void;
  interactBuilding(): void;
  currentBuildingModule: any;
  isCurrentBuildingDefaultExport: boolean;
  fetchBuilding(building: string);
  characterMatch: undefined | Character;
  characterMatchUIChanged: boolean;
  checkCharacter(): void;
  interactCharacter(): void;
}

export const inputHandler: IInputHandler = {
  buildingAssetsRecord: {
    [Buildings.BAKERY]: {},
    [Buildings.STOCKPILE]: {},
    [Buildings.TRAVELBUREAU]: {},
    [Buildings.SMITHY]: {},
    [Buildings.MINE]: {
      script: 'mine',
    },
    [Buildings.CROPS]: {
      script: 'crops',
    },
    [Buildings.ZINSSTORE]: {
      script: 'zinsstore',
    },
    [Buildings.MERCHANT]: {
      script: 'merchant',
    },
    [Buildings.WORKFORCELODGE]: {},
    [Buildings.ARCHERYSHOP]: {},
    [Buildings.ARMORY]: {},
  },
  buildingMatch: <undefined | Building>undefined,
  buildingMatchUIChanged: false,
  checkBuilding(mouseinputX = 0, mouseinputY = 0) {
    this.buildingMatch = undefined;
    const px = GamePieces.player.xpos;
    const py = GamePieces.player.ypos;
    const candidates = GamePieces.spatialGrid.query(
      px - 200,
      py - 200,
      px + 200,
      py + 200,
    );
    for (let i = 0, n = candidates.length; i < n; i++) {
      const object = candidates[i];
      if (!(object instanceof Building) || object.type !== 'building') continue;
      if (
        py > object.diameterUp &&
        py < object.diameterDown &&
        px > object.diameterLeft &&
        px < object.diameterRight &&
        Math.abs(py - object.diameterDown) < 32
      ) {
        this.buildingMatch = object;
        break;
      }
    }
    if (this.buildingMatch) {
      const buildingName = this.buildingMatch.displayName;
      gameEventBus.emit('HUD_BUILDING_PROMPT_UPDATE', { buildingName });
      this.buildingMatchUIChanged = true;
    } else if (this.buildingMatchUIChanged) {
      gameEventBus.emit('HUD_BUILDING_PROMPT_UPDATE', { buildingName: null });
      this.buildingMatchUIChanged = false;
    }
  },
  interactBuilding() {
    if (tutorial.onGoing) {
      GameLogger.addMessage(
        'This building can not be accessed on tutorial island',
        true,
      );
    }
  },
  currentBuildingModule: undefined,
  isCurrentBuildingDefaultExport: false,

  async fetchBuilding(building: BuildingName) {
    Game.properties.inBuilding = true;
    Game.properties.building = building;

    useConversationStore().triggerEndConversation();

    if (isVuePage(building)) {
      gameEventBus.emit('RENDER_BUILDING', {
        building: building,
      });
      return;
    }

    ClientOverlayInterface.loadingScreen();
    let response:
      | string
      | ReturnType<typeof buildingDataPreloader.getBuildingCache>;

    const cache = buildingDataPreloader.getBuildingCache(building);
    if (cache !== undefined && 'view' in cache) {
      response = cache.view;
    } else {
      const buildingResponse = await fetch('/' + building)
        .then(response => {
          if (!response.ok) {
            return Promise.reject(
              `Building ${building} could not be retrieved: ` +
                response.statusText,
            );
          }
          return response.text();
        })
        .catch((error: unknown) => {
          let message = 'Unknown error';
          if (error instanceof Error) {
            message = error.message;
          }
          reportError(
            new Error(
              `Building ${building} could not be retrieved: ` + message,
            ),
          );
          return '';
        });
      response = buildingResponse;
    }

    let script: string = '';
    let css;
    let html: string;
    let link;
    let skipImport;
    if (this.buildingAssetsRecord[building]) {
      const buildingName = building;
      if ('script' in this.buildingAssetsRecord[buildingName]) {
        script = this.buildingAssetsRecord[buildingName].script;
      } else {
        skipImport = true;
      }
      css = this.buildingAssetsRecord[buildingName].stylesheets;
      html = response;
    } else {
      const dataArray = response.split('|');
      css = dataArray[0].trim();
      script = dataArray[1];
      html = dataArray[2];
    }

    // Support this until all buildings are updated
    if (css && (css.length > 2 || css !== '#')) {
      link = document.createElement('link');
      link.type = 'text/css';
      link.rel = 'stylesheet';
      link.href = 'public/css/' + css;
      document.getElementsByTagName('head')[0].appendChild(link);
    }

    gameEventBus.emit('RENDER_BUILDING', {
      building: building,
      content: html,
    });

    itemTitle.addItemClassEvents();
    const src = '/public/dist/js/buildingScripts/';
    if (skipImport == false && script.length === 0) {
      GameLogger.addMessage('Building could not be retrieved', true);
      return;
    }
    if (!shouldSkipImport(building)) {
      // stockpileModule.init();
      await import(src + script).then(data => {
        setUpTabList();
        if (typeof this.currentBuildingModule.default === 'function') {
          const classInstance = new this.currentBuildingModule.default();
          new ModuleTester(classInstance, Game.properties.building, {
            defaultExport: false,
          });
        } else if (this.currentBuildingModule.default.init) {
          this.currentBuildingModule.default.init();
          new ModuleTester(
            this.currentBuildingModule,
            Game.properties.building,
            { defaultExport: true },
          );
        }
        this.currentBuildingModule = data;
      });
    } else {
      // Make sure DOM is loaded before initializing building module
      await new Promise(resolve => setTimeout(resolve, 300));
      switch (building) {
        case 'archeryshop':
          this.currentBuildingModule = archeryShopModule;
          this.currentBuildingModule.init();
          break;
        case 'travelbureau':
          this.currentBuildingModule = travelBureauModule;
          this.currentBuildingModule.init();
          break;
        case 'bakery':
          this.currentBuildingModule = bakeryModule;
          this.currentBuildingModule.init();
          break;
        case 'mine':
          this.currentBuildingModule = new MineModule();
          break;
        case 'crops':
          this.currentBuildingModule = new CropsModule();
          break;
        case 'zinsstore':
          this.currentBuildingModule = zinsStoreModule;
          this.currentBuildingModule.init();
          break;

        case 'smithy':
          this.currentBuildingModule = smithyModule;
          this.currentBuildingModule.init();
          break;
        case 'workforcelodge':
          this.currentBuildingModule = workforceLodgeModule;
          this.currentBuildingModule.init();
          break;
      }
      if (import.meta.env.DEV) {
        new ModuleTester(this.currentBuildingModule, Game.properties.building, {
          defaultExport: this.isCurrentBuildingDefaultExport,
        });
      }
    }
    return;
  },
  characterMatch: <undefined | Character>undefined,
  characterMatchUIChanged: false,
  checkCharacter() {
    this.characterMatch = undefined;
    const px = GamePieces.player.xpos;
    const py = GamePieces.player.ypos;
    const candidates = GamePieces.spatialGrid.query(
      px - 32,
      py - 32,
      px + 32,
      py + 32,
    );
    for (let i = 0, n = candidates.length; i < n; i++) {
      const obj = candidates[i];
      if (!(obj instanceof Character)) continue;
      if (Math.abs(px - obj.x) < 32 && Math.abs(py - obj.y) < 32) {
        this.characterMatch = obj;
        break;
      }
    }

    if (this.characterMatch) {
      gameEventBus.emit('HUD_CONVERSATION_PROMPT_UPDATE', {
        characterName: this.characterMatch.displayName,
      });
      this.characterMatchUIChanged = true;
    } else if (this.characterMatchUIChanged) {
      gameEventBus.emit('HUD_CONVERSATION_PROMPT_UPDATE', {
        characterName: null,
      });
      this.characterMatchUIChanged = false;
    }
  },
  interactCharacter() {
    if (this.characterMatch === undefined) return;
    if (!this.characterMatch.hasConversation) return;

    if (this.characterMatch.src.split('.png')[0] === 'hassen') {
      tutorial.checkStep();
    } else if (
      tutorial.onGoing &&
      this.characterMatch.src.includes('tutorial_sailor')
    ) {
      GameLogger.addMessage(
        'That person is not interested in talking to you now',
        true,
      );
    } else {
      useConversationStore().triggerLoadConversation(
        this.characterMatch.displayName,
      );
    }
  },
};

addModuleTester(inputHandler, 'inputHandler');
