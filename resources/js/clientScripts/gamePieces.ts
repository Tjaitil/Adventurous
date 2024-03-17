import { inputHandler } from './inputHandler';
import { GameObject } from '../types/gamepieces/GameObject';
import { BaseStaticGameObject } from '../gamepieces/BaseStaticGameObject';
import { Building } from '../gamepieces/Building';
import { Character, ICharacter } from '../gamepieces/Character';
import {
    IDaqloonFightingArea,
    DaqloonFightingArea,
} from '../gamepieces/DaqloonFightingArea';
import { Daqloon } from '../gamepieces/Daqloon';
import { Player } from '../gamepieces/Player';
import viewport from './viewport';
import { Item } from '../gamepieces/Item';
import { StaticGameObject } from '../types/gamepieces/StaticGameObject';
import { WorldMapData } from '../types/Advclient';
import { HUD } from './HUD';
import { addModuleTester } from '@/devtools/ModuleTester';

export type gameObjectTypes = Character | Building | BaseStaticGameObject;

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
    nearCharacters: <Character[]>[],
    nearBuildings: <Building[]>[],
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
        if (
            daqloonFightingAreas !== undefined &&
            daqloonFightingAreas.length > 0
        ) {
            this.daqloon_fighting_area = new DaqloonFightingArea(
                daqloonFightingAreas[0],
            );
            this.daqloon = this.daqloon_fighting_area.loadDaqloons();
            this.daqloon_fighting_area.findHuntingDaqloon();
            // checkDaqloon(GamePieces.daqloon_fighting_area.daqloon_amount);
        } else {
            this.daqloon = [];
            HUD.elements.huntedLocator.innerHTML = '';
        }
    },
    loadStaticPieces(initObjects: GameObject[]) {
        initObjects.forEach(object => {
            let instantiatedObject;

            switch (object.type) {
                case 'character':
                    instantiatedObject = new Character(<ICharacter>object);
                    this.characters.push(instantiatedObject);
                    break;
                case 'building':
                    instantiatedObject = new Building(<Building>object);
                    this.buildings.push(instantiatedObject);
                    break;
                default:
                    instantiatedObject = new BaseStaticGameObject(
                        <StaticGameObject>object,
                    );
                    break;
            }
            this.objects.push(instantiatedObject);
        });

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
        viewport.resetObjectLayer();

        for (const GamePiece of GamePieces.visibleObjects) {
            let drawContext;

            // if(GamePiece.type === "building") GamePiece;
            // If building is behind player, then draw on the first canvas instead of the third
            if (GamePiece.diameterDown < GamePieces.player.diameterDown) {
                drawContext = 'background';
            } else {
                drawContext = 'frontObjects';
            }
            if (GamePiece.type === 'character') {
                // drawContext.imageSmoothingEnabled = false;
                viewport.drawObject(
                    drawContext,
                    GamePiece.sprite,
                    GamePiece.drawX - GamePieces.player.xMovement,
                    GamePiece.drawY - GamePieces.player.yMovement,
                    GamePiece.width,
                    GamePiece.height,
                );
                // TODO: Fix a better solution for this
                // viewport.layer.text.font = "30px Comic Sans MS";
                // viewport.layer.frontObjects.fillText(
                //     GamePiece.displayName,
                //     (GamePiece.drawX - GamePieces.player.xMovement) +
                //     (GamePiece.width / 2) -
                //     ((GamePiece.displayName.length / 2) * 5),
                //     GamePiece.drawY - GamePieces.player.yMovement,
                // );
            } else {
                viewport.drawObject(
                    drawContext,
                    GamePiece.sprite,
                    Math.round(GamePiece.drawX - GamePieces.player.xMovement),
                    Math.round(GamePiece.drawY - GamePieces.player.yMovement),
                    GamePiece.width,
                    GamePiece.height,
                );
            }
        }

        inputHandler.checkCharacter();
        inputHandler.checkBuilding();

        if (draw === true) {
            addModuleTester(GamePieces.objects, 'GamePieces');
            addModuleTester(GamePieces.visibleObjects, 'visibleObjects');
            for (let i = 0, n = GamePieces.objects.length; i < n; i++) {
                viewport.layer.frontObjects.fillStyle = 'red';
                viewport.layer.frontObjects.fillRect(
                    GamePieces.objects[i].drawX -
                        GamePieces.player.xMovement / viewport.scale,
                    GamePieces.objects[i].drawY -
                        GamePieces.player.yMovement / viewport.scale,
                    GamePieces.objects[i].width,
                    GamePieces.objects[i].height,
                );
                viewport.layer.frontObjects.font = '10px Comic Sans MS';
                viewport.layer.frontObjects.fillStyle = 'white';
                viewport.layer.frontObjects.fillText(
                    i + ' | ' + GamePieces.objects[i].id,
                    GamePieces.objects[i].drawX -
                        GamePieces.player.xMovement / viewport.scale +
                        GamePieces.objects[i].width / 2,
                    GamePieces.objects[i].drawY +
                        GamePieces.objects[i].height / 2 -
                        GamePieces.player.yMovement / viewport.scale,
                );
            }
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
document.getElementById('draw_checkbox').addEventListener('change', event => {
    const element = <HTMLInputElement>event.currentTarget;
    draw = element.checked;
});
