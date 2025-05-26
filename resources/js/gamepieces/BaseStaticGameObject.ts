import { StaticGameObject } from '../types/gamepieces/StaticGameObject';
import viewport from '../clientScripts/viewport';
import { NonDrawingTypes } from './NonDrawingTypes';
import { AssetPaths } from '../clientScripts/ImagePath';

export class BaseStaticGameObject implements StaticGameObject {
  public type: string;
  public sprite: HTMLImageElement;
  public src: string;
  public drawX: number;
  public drawY: number;
  public visible: boolean;
  public diameterUp: number;
  public diameterLeft: number;
  public diameterRight: number;
  public diameterDown: number;
  public x: number;
  public y: number;
  public width: number;
  public height: number;
  public noCollision: boolean;
  public id: boolean;
  public displayName: string;

  constructor(initObjectData: StaticGameObject) {
    this.visible = initObjectData.visible;
    this.type = initObjectData.type;

    this.diameterUp = initObjectData.diameterUp;
    this.diameterRight = initObjectData.diameterRight;
    this.diameterDown = initObjectData.diameterDown;
    this.diameterLeft = initObjectData.diameterLeft;

    this.width = initObjectData.width;
    this.height = initObjectData.height;
    this.x = initObjectData.x;
    this.y = initObjectData.y;
    this.src = initObjectData.src;

    this.id = initObjectData.id;

    this.drawX = Math.round(initObjectData.x - viewport.offsetX);
    this.drawY = Math.round(initObjectData.y - viewport.offsetY);

    if (this.src) {
      this.displayName = this.src.split('.png')[0];
    }

    this.noCollision = initObjectData.noCollision;
    this.sprite = new Image(this.width, this.height);

    this.sprite.src = AssetPaths.getImagePath(this.src);
    if (!this.src && !NonDrawingTypes.includes(this.type)) {
      console.error('No image source found for ' + initObjectData);
    }
    // check source for missing format
    if (this.sprite.src.includes('.png') === false) this.sprite.src += '.png';
  }
}
