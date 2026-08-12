import { inputHandler } from './inputHandler';
import type { GameObject } from '../types/gamepieces/GameObject';
import { BaseStaticGameObject } from '../gamepieces/BaseStaticGameObject';
import { Building } from '../gamepieces/Building';
import type { ICharacter } from '../gamepieces/Character';
import { Character } from '../gamepieces/Character';
import type { IDaqloonFightingArea } from '../gamepieces/DaqloonFightingArea';
import { DaqloonFightingArea } from '../gamepieces/DaqloonFightingArea';
import type { Daqloon } from '../gamepieces/Daqloon';
import { Player } from '../gamepieces/Player';
import viewport from './viewport';
import { SpatialGrid } from './SpatialGrid';
import type { Item } from '../gamepieces/Item';
import type { StaticGameObject } from '../types/gamepieces/StaticGameObject';
import type { WorldMapData } from '../types/Advclient';
import { HUD } from './HUD';
import { addModuleTester } from '@/devtools/ModuleTester';

export type gameObjectTypes = Character | Building | BaseStaticGameObject;
export type gridObjectTypes = gameObjectTypes | Daqloon;

let draw = false;

// TODO: Create class
export const GamePieces = {
  nonDrawingTypes: ['figure', 'nc_object', 'start_point', 'daqloon'],
  assets: [],
  events: [],
  items: [] as Item[],
  objects: [] as gameObjectTypes[],
  daqloon: [] as Daqloon[],
  buildings: [] as Building[],
  characters: [] as Character[],
  daqloon_fighting_area: undefined as undefined | DaqloonFightingArea,
  spatialGrid: new SpatialGrid<gridObjectTypes>(),
  player: new Player(),
  reset() {
    this.objects = [];
    this.daqloon = [];
    this.characters = [];
    this.spatialGrid.reset();
    this.daqloon_fighting_area = undefined;
  },
  loadAssets(xbase, ybase, mapData: WorldMapData) {
    console.log(mapData);
    this.player.load(xbase, ybase, null);
    this.loadDaqloonFightingArea(mapData.daqloon_fighting_areas);
    this.loadStaticPieces(mapData.objects);
  },
  loadDaqloonFightingArea(daqloonFightingAreas: IDaqloonFightingArea[]) {
    if (daqloonFightingAreas !== undefined && daqloonFightingAreas.length > 0) {
      this.daqloon_fighting_area = new DaqloonFightingArea(
        daqloonFightingAreas[0],
      );
      this.daqloon = this.daqloon_fighting_area.loadDaqloons();
      this.daqloon_fighting_area.findHuntingDaqloon();
      // checkDaqloon(GamePieces.daqloon_fighting_area.daqloon_amount);
    } else {
      this.daqloon = [];
    }
  },
  loadStaticPieces(initObjects: GameObject[]) {
    GamePieces.objects = [];
    GamePieces.buildings = [];
    GamePieces.characters = [];
    initObjects.forEach(object => {
      let instantiatedObject;

      switch (object.type) {
        case 'character':
          instantiatedObject = new Character(<ICharacter>object);
          this.characters.push(instantiatedObject);
          break;
        case 'building':
          instantiatedObject = new Building(object);
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

    this.spatialGrid.reset();
    for (const obj of this.objects) this.spatialGrid.insert(obj);
    for (const daqloon of this.daqloon) this.spatialGrid.insert(daqloon);
  },
  init() {
    GamePieces.drawStaticPieces();
    GamePieces.player.draw();
  },
  drawStaticPieces() {
    viewport.resetObjectLayer();

    const margin = 50;
    const drawable = this.spatialGrid
      .query(
        this.player.xpos - viewport.width - margin,
        this.player.ypos - viewport.height - margin,
        this.player.xpos + viewport.width + margin,
        this.player.ypos + viewport.height + margin,
      )
      .filter(obj => !this.nonDrawingTypes.includes(obj.type) && obj.visible)
      .sort((a, b) => a.diameterDown - b.diameterDown);

    for (const gamePiece of drawable) {
      const drawContext =
        gamePiece.diameterDown < this.player.diameterDown
          ? 'background'
          : 'frontObjects';

      viewport.drawObject(
        drawContext,
        gamePiece.sprite,
        gamePiece.type === 'character'
          ? gamePiece.drawX - this.player.xMovement
          : Math.round(gamePiece.drawX - this.player.xMovement),
        gamePiece.type === 'character'
          ? gamePiece.drawY - this.player.yMovement
          : Math.round(gamePiece.drawY - this.player.yMovement),
        gamePiece.width,
        gamePiece.height,
      );
    }

    inputHandler.checkCharacter();
    inputHandler.checkBuilding();

    if (draw) {
      addModuleTester(GamePieces.objects, 'GamePieces');
      addModuleTester(drawable, 'visibleObjects');
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
window.addEventListener('load', () => {
  // TODO: This of course be refactored to not rely on setTimeout
  window.setTimeout(() => {
    document
      .getElementById('draw_checkbox')
      .addEventListener('change', event => {
        const element = <HTMLInputElement>event.currentTarget;
        draw = element.checked;
      });
  }, 5000);
});
