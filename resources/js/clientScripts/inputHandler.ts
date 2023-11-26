import { ModuleTester } from './../devtools/ModuleTester.js';
import { Character } from './../gamepieces/Character';
import { controls } from "./controls.js";
import { ClientOverlayInterface } from "./clientOverlayInterface.js";
import { tutorial } from "./tutorial.js";
import { itemTitle } from "../utilities/itemTitle.js";
import { Game } from "../advclient.js";
import { gameLogger } from "../utilities/gameLogger.js";
import { conversation } from "./conversation.js";
import { GamePieces } from "./gamePieces.js";
import { Building } from "../gamepieces/Building.js";
import { HUD } from './HUD.js';
import { setUpTabList } from '../utilities/tabs.js';


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

type BuildingName = `${Buildings}`;

interface BuildingAssetsTypes {
    stylesheets?: string[];
    script: string;
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
    fetchBuilding(building: string): Promise<string>;
    characterMatch: undefined | Character;
    characterMatchUIChanged: boolean;
    checkCharacter(): void;
    interactCharacter(): void;
}

export const inputHandler: IInputHandler = {
    buildingAssetsRecord: {
        [Buildings.BAKERY]: {
            "script": "bakery.js"
        },
        [Buildings.TRAVELBUREAU]: {
            "script": "travelbureau.js"
        },
        [Buildings.STOCKPILE]: {
            "script": "stockpile.js",
        },
        [Buildings.MINE]: {
            "script": "mine.js",
        },
        [Buildings.CROPS]: {
            "script": "crops.js",
        },
        [Buildings.ZINSSTORE]: {
            "script": "zinsstore.js",
        },
        [Buildings.MERCHANT]: {
            "script": "merchant.js",
        },
        [Buildings.WORKFORCELODGE]: {
            "script": "workforcelodge.js",
        }
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
            gameLogger.addMessage("This building can not be accessed on tutorial island", true);
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
        await fetch("handlers/handler_v.php?" + new URLSearchParams({ building: building }))
            .then((response) => {
                if (!response.ok) throw new Error("Something unexpected happened. Please try again");
                return response.text();
            })
            .then(async (data) => {
                let script: string;
                let css;
                let html: string;
                let link;

                if (this.buildingAssetsRecord[building]) {
                    let buildingName = building as BuildingName;
                    script = this.buildingAssetsRecord[buildingName].script;
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
                if (script.length === 0) {
                    gameLogger.addMessage("Building could not be retrieved", true);
                    return;
                }
                const module = await import(src + script).then((data) => {
                    this.currentBuildingModule = data;
                    setUpTabList();
                    if (typeof this.currentBuildingModule.default === "function") {
                        let classInstance = (new this.currentBuildingModule.default());
                        new ModuleTester(classInstance, Game.properties.building, { defaultExport: false, });
                    } else if (this.currentBuildingModule.default.init) {
                        this.currentBuildingModule.default.init();
                        new ModuleTester(this.currentBuildingModule, Game.properties.building, { defaultExport: true, });
                    }
                    console.log(this.currentBuildingModule);
                });
            })
            .catch(error => {
                console.log(error);
                // closeNews();
                // alert(error);
                // return;
            })
        return building;
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
            gameLogger.addMessage("That person is not interested in talking to you now", true);
        } else {
            conversation.loadConversation(this.characterMatch.displayName, "", false);
        }
    },
};

(<any>window).inputHandler = inputHandler;