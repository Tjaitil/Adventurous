import { inputHandler } from "./inputHandler.js";
import { GameObject } from "../types/gamepieces/GameObject.js";
import { BaseStaticGameObject } from "../gamepieces/BaseStaticGameObject.js";
import { Building } from "../gamepieces/Building.js";
import { Character, ICharacter } from "../gamePieces/Character.js";
import { IDaqloonFightingArea, DaqloonFightingArea } from "../GamePieces/DaqloonFightingArea.js";
import { Daqloon } from "../gamePieces/Daqloon.js";
import { Player } from "../gamepieces/Player.js";
import viewport from "./viewport.js";
import { Item } from "../gamepieces/Item.js";
import { StaticGameObject } from "../types/gamepieces/StaticGameObject";
import { WorldMapData } from "../types/Advclient.js";

export type gameObjectTypes = Character | Building | BaseStaticGameObject;

enum gamePiecesTypes {
    "character",
    "building",
}

let draw = false;

// TODO: Create class
export const GamePieces = {
    events: [],
    items: [] as Item[],
    objects: [] as gameObjectTypes[],
    daqloon: [] as Daqloon[],
    buildings: [] as Building[],
    characters: [] as Character[],
    daqloon_fighting_area: undefined as undefined | DaqloonFightingArea,
    visibleObjects: [] as gameObjectTypes[],
    nearObjects: [] as gameObjectTypes[],
    player: new Player(),
    reset() {
        this.objects = [];
        this.daqloon = [];
        this.characters = [];
        this.visibleObjects = [];
        this.nearObjects = [];
        this.daqloon_fighting_area = undefined;
    },
    loadAssets(xbase, ybase, mapData: WorldMapData) {
        this.player.load(xbase, ybase, null);
        this.loadDaqloonFightingArea(mapData.daqloon_fighting_areas);
        this.loadStaticPieces(mapData.objects);
    },
    loadDaqloonFightingArea(daqloonFightingAreas: IDaqloonFightingArea[]) {
        if (daqloonFightingAreas !== undefined && daqloonFightingAreas.length > 0) {
            this.daqloon_fighting_area = new DaqloonFightingArea(daqloonFightingAreas[0]);
            this.daqloon = this.daqloon_fighting_area.loadDaqloons();
            // checkDaqloon(GamePieces.daqloon_fighting_area.daqloon_amount);
        } else {
            this.daqloon = [];
            document.getElementById("HUD_hunted_locater").innerHTML = "";
        }
    },
    loadStaticPieces(initObjects: GameObject[]) {
        initObjects.forEach((object) => {
            let instantiatedObject;

            switch (object.type) {
                case "character":
                    instantiatedObject = new Character(<ICharacter>object);
                    this.characters.push(instantiatedObject);
                    break;
                case "building":
                    instantiatedObject = new Building(<Building>object);
                    this.buildings.push(instantiatedObject);
                    break;
                default:
                    instantiatedObject = new BaseStaticGameObject(<StaticGameObject>object);
                    break;
            }
            this.objects.push(instantiatedObject);
        });

        // for(let i = 0, n = GamePieces.objects.length; i < n; i++) {
        //     if (GamePieces.objects[i].src != undefined && GamePieces.objects[i].src.length > 1) {
        //         if (GamePieces.objects[i].type === 'character') {
        //             GamePieces.objects[i].width = 38;
        //             GamePieces.objects[i].width = 38;
        //             GamePieces.objects[i].height = 38;
        //             GamePieces.objects[i].x -= 6;
        //             GamePieces.objects[i].y -= 6;
        //         }
        //         if (GamePieces.objects[i].src.indexOf('.png') == -1) GamePieces.objects[i].src += '.png';
        //         GamePieces.objects[i].sprite.src = "public/images/" + GamePieces.objects[i].src;
        //     }
        //     GamePieces.objects[i].width *= viewport.scale;
        //     GamePieces.objects[i].height *= viewport.scale;
        //     GamePieces.objects[i].drawX = Math.round(GamePieces.objects[i].x - viewport.offsetX);
        //     GamePieces.objects[i].drawY = Math.round(GamePieces.objects[i].y - viewport.offsetY);
        //     if (GamePieces.objects[i].type == "building") {
        //         let object = GamePieces.objects[i] as ICharacter;
        //         GamePieces.buildings.push(GamePieces.objects[i]);
        //     }
        //     else if (GamePieces.objects[i].type == "character") {
        //         let object = GamePieces.objects[i] as ICharacter;
        //         GamePieces.characters.push(object);
        //     }
        // }
        GamePieces.objects.sort((a, b) => {
            return a.diameterDown - b.diameterDown;
        });
    },
    init() {
        GamePieces.drawStaticPieces();
        GamePieces.player.draw();
    },
    drawStaticPieces() {
        // buildingMatch variable is to check if there is at building that the player can enter
        let buildingMatch = false;
        let personMatch = false;
        let person = null;
        viewport.resetObjectLayer();
        // console.log('x', GamePieces.player.xMovement);
        for (let i = 0, n = GamePieces.visibleObjects.length; i < n; i++) {
            // console.log(GamePieces.visibleObjects[i]);
            if (
                GamePieces.visibleObjects[i].visible === true &&
                GamePieces.visibleObjects[i].type !== "figure" &&
                ["desert_dune", "nc_object"].indexOf(GamePieces.visibleObjects[i].type) === -1 &&
                GamePieces.visibleObjects[i].src.length > 1
            ) {
                let drawContext;

                // if(GamePieces.visibleObjects[i].type === "building") console.log(GamePieces.visibleObjects[i]);
                // If building is behind player, then draw on the first canvas instead of the third
                if (GamePieces.visibleObjects[i].diameterDown < GamePieces.player.diameterDown) {
                    drawContext = "background";
                } else {
                    drawContext = "frontObjects";
                }
                if (GamePieces.visibleObjects[i].type === "character") {
                    // drawContext.imageSmoothingEnabled = false;
                    viewport.drawObject(
                        drawContext,
                        GamePieces.visibleObjects[i].sprite,
                        GamePieces.visibleObjects[i].drawX - GamePieces.player.xMovement,
                        GamePieces.visibleObjects[i].drawY - GamePieces.player.yMovement,
                        GamePieces.visibleObjects[i].width,
                        GamePieces.visibleObjects[i].height
                    );
                } else {
                    viewport.drawObject(
                        drawContext,
                        GamePieces.visibleObjects[i].sprite,
                        Math.round(GamePieces.visibleObjects[i].drawX - GamePieces.player.xMovement),
                        Math.round(GamePieces.visibleObjects[i].drawY - GamePieces.player.yMovement),
                        GamePieces.visibleObjects[i].width,
                        GamePieces.visibleObjects[i].height
                    );
                }
            }
        }
        inputHandler.checkCharacter();
        inputHandler.checkBuilding();

        if (draw === true) {
            (<any>window).gamePieces = GamePieces.objects;
            (<any>window).visibleObjects = GamePieces.visibleObjects;
            for (let i = 0, n = GamePieces.objects.length; i < n; i++) {
                viewport.layer.frontObjects.fillStyle = "red";
                viewport.layer.frontObjects.fillRect(
                    GamePieces.objects[i].drawX - GamePieces.player.xMovement / viewport.scale,
                    GamePieces.objects[i].drawY - GamePieces.player.yMovement / viewport.scale,
                    GamePieces.objects[i].width,
                    GamePieces.objects[i].height
                );
                viewport.layer.frontObjects.font = "10px Comic Sans MS";
                viewport.layer.frontObjects.fillStyle = "white";
                viewport.layer.frontObjects.fillText(
                    i + " | " + GamePieces.objects[i].id,
                    GamePieces.objects[i].drawX -
                        GamePieces.player.xMovement / viewport.scale +
                        GamePieces.objects[i].width / 2,
                    GamePieces.objects[i].drawY +
                        GamePieces.objects[i].height / 2 -
                        GamePieces.player.yMovement / viewport.scale
                );
            }
            // for(let i = 0; i < GamePieces.daqloon_fighting_area.length; i++) {

            // }
        }
    },
    drawDaqloons() {
        if (this.daqloon.length > 0) {
            for (const daqloon of this.daqloon) {
                daqloon.draw();
            }
        }
    },
};
document.getElementById("draw_checkbox").addEventListener("change", (event) => {
    let element = <HTMLInputElement>event.currentTarget;
    draw = element.checked;
});
