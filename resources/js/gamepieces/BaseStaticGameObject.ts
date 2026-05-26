import viewport from '../clientScripts/viewport';
import { AssetPaths } from '../clientScripts/ImagePath';
import { GamePieces } from '@/clientScripts/gamePieces';
import type { Character } from '@/types/gamepieces/Character';
import type {
  IBuildingGameObject,
  GameObject,
  GameObjectType,
  StaticGameObject,
} from '@/types/gamepieces/GameObject';

export class BaseStaticGameObject implements GameObject {
  public type: GameObjectType;
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
  public id: number;

  constructor(
    initObjectData: StaticGameObject | Character | IBuildingGameObject,
  ) {
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

    this.noCollision = initObjectData.noCollision;
    this.sprite = new Image(this.width, this.height);

    this.sprite.src = AssetPaths.getImagePath(this.src);
    if (!this.src && !GamePieces.nonDrawingTypes.includes(this.type)) {
      console.error('No image source found for ' + this.src);
    }
    // check source for missing format
    if (!this.sprite.src.includes('.png')) this.sprite.src += '.png';
  }
}
