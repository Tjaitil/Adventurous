import { ModuleTester } from './../devtools/ModuleTester';
import { Character } from './../gamepieces/Character';
import { controls } from "./controls";
import { ClientOverlayInterface } from "./clientOverlayInterface";
import { tutorial } from "./tutorial";
import { itemTitle } from "../utilities/itemTitle";
import { Game } from "../advclient";
import { GameLogger } from "../utilities/GameLogger";
import { conversation } from "./conversation";
import { GamePieces } from "./gamePieces";
import { Building } from "../gamepieces/Building";
import { HUD } from './HUD';
import { setUpTabList } from '../utilities/tabs';
import stockpileModule from '../buildingScripts/stockpile';
import travelBureauModule from '../buildingScripts/travelbureau';
import bakeryModule from '../buildingScripts/bakery';
import MineModule from '../buildingScripts/mine';
import CropsModule from '../buildingScripts/crops';
import zinsStoreModule from '../buildingScripts/zinsstore';
import merchantModule from '../buildingScripts/merchant';
import workforceLodgeModule from '../buildingScripts/workforcelodge';


enum Buildings {
    BAKERY = "bakery",
    TRAVELBUREAU = "travelbureau",
    STOCKPILE = "stockpile",
    MINE = "mine",
    CROPS = "crops",
    ZINSSTORE = "zinsstore",
    MERCHANT = "merchant",
    WORKFORCELODGE = "workforcelodge",
}

type BuildingModuleMapping = {
    "bakery": typeof bakeryModule,
    "travelbureau": typeof travelBureauModule,
    "stockpile": typeof stockpileModule,
    "mine": typeof MineModule,
    "crops": typeof CropsModule,
    "zinsstore": typeof zinsStoreModule,
    "merchant": typeof merchantModule,
    "workforcelodge": typeof workforceLodgeModule,
}

function shouldSkipImport(building: string) {
    return ["stockpile", "travelbureau", "bakery", "mine", "crops", "zinsstore", "merchant", "workforcelodge"].includes(building);
}

type BuildingName = keyof BuildingModuleMapping;

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
    mapBuildingName(name: string): string;
    currentBuildingModule: any;
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
        [Buildings.MINE]: {
            "script": "mine",
        },
        [Buildings.CROPS]: {
            "script": "crops",
        },
        [Buildings.ZINSSTORE]: {
            "script": "zinsstore",
        },
        [Buildings.MERCHANT]: {
            "script": "merchant",
        },
        [Buildings.WORKFORCELODGE]: {}
    },
    buildingMatch: <undefined | Building>undefined,
    buildingMatchUIChanged: false,
    checkBuilding(mouseinputX = 0, mouseinputY = 0) {
        this.buildingMatch = undefined;
        for (let i = 0, n = GamePieces.nearBuildings.length; i < n; i++) {
            let object = GamePieces.buildings[i];
            if (
                GamePieces.player.ypos > object.diameterUp &&
                GamePieces.player.ypos < object.diameterDown &&
                GamePieces.player.xpos > object.diameterLeft &&
                GamePieces.player.xpos < object.diameterRight &&
                Math.abs(GamePieces.player.ypos - object.diameterDown) < 32
            ) {
                this.buildingMatch = object;
                break;
            }
        }
        if (this.buildingMatch) {
            HUD.elements.control_text_building.innerHTML =
                controls.enterText + " " + this.mapBuildingName(this.buildingMatch.displayName);
            this.buildingMatchUIChanged = true;
        } else if (this.buildingMatchUIChanged && !this.buildingMatch) {
            HUD.elements.control_text_building.innerHTML = controls.enterButton;
            this.buildingMatchUIChanged = false;
        }
    },
    interactBuilding() {
        if (tutorial.onGoing) {
            GameLogger.addMessage("This building can not be accessed on tutorial island", true);
        }
    },
    mapBuildingName(name: string) {
        let buildingName;
        switch (name) {
            case "adventure base":
            case "adventures base desert":
                buildingName = "adventures";
                break;
            case "stockpile desert":
                buildingName = "stockpile";
                break;
            case "merchant desert":
                buildingName = "merchant";
                break
            default:
                buildingName = name;
                break;
        }
        return buildingName;
    },
    currentBuildingModule: undefined,

    async fetchBuilding(building: string) {
        building = this.mapBuildingName(building.trim());
        Game.properties.inBuilding = true;
        Game.properties.building = building;

        conversation.endConversation();

        ClientOverlayInterface.loadingScreen();

        await fetch("/" + building)
            .then((response) => {
                if (!response.ok) throw new Error("Something unexpected happened. Please try again");
                return response.text();
            })
            .then(async (data) => {
                let script: string;
                let css;
                let html: string;
                let link;
                let skipImport;

                if (this.buildingAssetsRecord[building]) {
                    let buildingName = building as BuildingName;
                    if('script' in this.buildingAssetsRecord[buildingName]) {
                        script = this.buildingAssetsRecord[buildingName].script;
                    } else {
                        skipImport = true;
                    }
                    css = this.buildingAssetsRecord[buildingName].stylesheets;
                    html = data;
                } else {

                    let dataArray = data.split("|");
                    css = dataArray[0].trim();
                    script = dataArray[1];
                    html = dataArray[2];
                }

                // Support this until all buildings are updated
                if (css && (css.length > 2 || css !== "#")) {
                    link = document.createElement("link");
                    link.type = "text/css";
                    link.rel = "stylesheet";
                    link.href = "public/css/" + css;
                    document.getElementsByTagName("head")[0].appendChild(link);
                }
                ClientOverlayInterface.show(html);
                itemTitle.addItemClassEvents();
                const src = '/public/dist/js/buildingScripts/';
                if (skipImport == false && script.length === 0) {
                    GameLogger.addMessage("Building could not be retrieved", true);
                    return;
                }
                if (!shouldSkipImport(building)) {
                    // stockpileModule.init();
                    await import(src + script).then((data) => {
                        setUpTabList();
                        if (typeof this.currentBuildingModule.default === "function") {
                            let classInstance = (new this.currentBuildingModule.default());
                            new ModuleTester(classInstance, Game.properties.building, { defaultExport: false, });
                        } else if (this.currentBuildingModule.default.init) {
                            this.currentBuildingModule.default.init();
                            new ModuleTester(this.currentBuildingModule, Game.properties.building, { defaultExport: true, });
                        }
                        this.currentBuildingModule = data;
                    });
                } else {
                    switch (building) {
                        case "stockpile":
                            this.currentBuildingModule = stockpileModule;
                            this.currentBuildingModule.init();
                            break;
                        case "travelbureau":
                            this.currentBuildingModule = travelBureauModule;
                            this.currentBuildingModule.init();
                            break;
                        case "bakery":
                            this.currentBuildingModule = bakeryModule;
                            this.currentBuildingModule.init();
                            break;
                        case "mine":
                            this.currentBuildingModule = new MineModule();
                            break;
                        case "crops":
                            this.currentBuildingModule = new CropsModule();
                            break;
                        case "zinsstore":
                            this.currentBuildingModule = zinsStoreModule;
                            this.currentBuildingModule.init();
                            break;
                        case "merchant":
                            this.currentBuildingModule = merchantModule;
                            this.currentBuildingModule.init();
                            break;
                        case "workforcelodge":
                            this.currentBuildingModule = workforceLodgeModule;
                            this.currentBuildingModule.init();
                            break;
                    }
                }
            })
            .catch(error => {
                console.log(error);
                // closeNews();
                // alert(error);
                // return;
            })
    },
    characterMatch: <undefined | Character>null,
    characterMatchUIChanged: false,
    checkCharacter() {
        this.characterMatch = undefined;
        for (let i = 0, n = GamePieces.nearCharacters.length; i < n; i++) {
            if (
                Math.abs(GamePieces.player.xpos - GamePieces.nearCharacters[i].x) < 32 &&
                Math.abs(GamePieces.player.ypos - GamePieces.nearCharacters[i].y) < 32 &&
                GamePieces.characters[i].type === "character"
            ) {
                this.characterMatch = GamePieces.nearCharacters[i];
                break;
            }
        }

        if (this.characterMatch) {
            HUD.elements.control_text_conversation.innerHTML =
                controls.personText + " " + this.characterMatch.displayName;
            this.characterMatchUIChanged = true;
        } else if (this.characterMatchUIChanged && !this.characterMatch) {
            HUD.elements.control_text_conversation.innerHTML = controls.personButton;
            this.characterMatchUIChanged = false;
        }
    },
    interactCharacter() {
        if (this.characterMatch === undefined) return;

        if (this.characterMatch.src.split(".png")[0] === "hassen") {
            tutorial.checkStep();
        } else if (tutorial.onGoing && this.characterMatch.src.includes("tutorial_sailor")) {
            GameLogger.addMessage("That person is not interested in talking to you now", true);
        } else {
            conversation.loadConversation(this.characterMatch.displayName);
        }
    },
};

(<any>window).inputHandler = inputHandler;