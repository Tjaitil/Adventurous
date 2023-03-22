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


export const inputHandler = {
    buildingMatch: <undefined | Building>undefined,
    checkBuilding(mouseinputX = 0, mouseinputY = 0) {
        this.buildingMatch = undefined;
        if (Game.properties.inBuilding != true && Game.properties.device == "pc") {
            for (let i = 0, n = GamePieces.buildings.length; i < n; i++) {
                let object = GamePieces.buildings[i];
                // TODO: Add support for mobile
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
        }
        if (this.buildingMatch) {
            document.getElementById("control_text_building").innerHTML =
                controls.enterText + " " + this.mapBuildingName(this.buildingMatch.displayName);
        } else {
            document.getElementById("control_text_building").innerHTML = controls.enterButton;
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

        conversation.endConversation();

        ClientOverlayInterface.loadingScreen();
        await fetch("handlers/handler_v.php?" + new URLSearchParams({ building: building }))
            .then((response) => {
                if (!response.ok) throw new Error("Something unexpected happened. Please try again");
                return response.text();
            })
            .then(async (data) => {
                let dataArray = data.split("|");
                let css = dataArray[0].trim();
                let script = dataArray[1];
                let html = dataArray[2];
                let link;
                if (css.length > 2 || css !== "#") {
                    Game.properties.building = building;
                    link = document.createElement("link");
                    link.type = "text/css";
                    link.rel = "stylesheet";
                    link.href = "public/css/" + css;
                    document.getElementsByTagName("head")[0].appendChild(link);
                }
                ClientOverlayInterface.show(html);
                itemTitle.addItemClassEvents();
                console.log(building);
                const src = '/public/dist/js/buildingScripts/';
                const module = await import(src + script).then((data) => {
                    console.log(data);
                    this.currentBuildingModule = data;
                    if (this.currentBuildingModule.default.init) {
                        this.currentBuildingModule.default.init();
                    }
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
    characterMatched: <undefined | Character>null,
    checkCharacter() {
        let characterMatch = null;

        for (let i = 0, n = GamePieces.characters.length; i < n; i++) {
            if (
                Math.abs(GamePieces.player.xpos - GamePieces.characters[i].x) < 32 &&
                Math.abs(GamePieces.player.ypos - GamePieces.characters[i].y) < 32 &&
                GamePieces.characters[i].type === "character"
            ) {
                characterMatch = this.characterMatched = GamePieces.characters[i];
                break;
            }
        }

        if (characterMatch) {
            document.getElementById("control_text_conversation").innerHTML =
                controls.personText + " " + this.characterMatched.displayName;
        } else {
            document.getElementById("control_text_conversation").innerHTML = controls.personButton;
        }
    },
    interactCharacter() {
        if (this.characterMatched === undefined) return;

        if (this.characterMatched.src.split(".png")[0] === "hassen") {
            tutorial.checkStep();
        } else if (tutorial.onGoing && this.characterMatched.src.includes("tutorial_sailor")) {
            gameLogger.addMessage("That person is not interested in talking to you now", true);
        } else {
            conversation.loadConversation(this.characterMatched.displayName, "", false);
        }
    },
};

(<any>window).inputHandler = inputHandler;