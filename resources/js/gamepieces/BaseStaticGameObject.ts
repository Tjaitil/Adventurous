import viewport from '../clientScripts/viewport';
import { AssetPaths } from '../clientScripts/ImagePath';
import { getTransformedImageUrl } from '@/clientScripts/spriteTransform';
import { GamePieces } from '@/clientScripts/gamePieces';
import type { SpriteTransform } from '@/clientScripts/spriteTransform';
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
  public xRotated: number | null;
  public yRotated: number | null;
  public widthRotated: number | null;
  public heightRotated: number | null;
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
    this.xRotated = initObjectData.xRotated ?? null;
    this.yRotated = initObjectData.yRotated ?? null;
    this.widthRotated = initObjectData.widthRotated ?? null;
    this.heightRotated = initObjectData.heightRotated ?? null;
    this.x = initObjectData.x;
    this.y = initObjectData.y;
    this.src = initObjectData.src;

    this.id = initObjectData.id;

    this.drawX = Math.round((this.xRotated ?? this.x) - viewport.offsetX);
    this.drawY = Math.round((this.yRotated ?? this.y) - viewport.offsetY);

    this.noCollision = initObjectData.noCollision;
    this.sprite = new Image(
      this.widthRotated ?? this.width,
      this.heightRotated ?? this.height,
    );

    this.sprite.src = AssetPaths.getImagePath(this.src);
    // check source for missing format
    if (!this.sprite.src.includes('.png')) this.sprite.src += '.png';

    const rotation = initObjectData.rotation ?? 0;
    const isTransformed =
      initObjectData.flipX || initObjectData.flipY || rotation % 360 !== 0;
    if (initObjectData.gid !== undefined && isTransformed) {
      this.transformSpriteOnLoad({
        flipX: !!initObjectData.flipX,
        flipY: !!initObjectData.flipY,
        rotation,
        box: {
          width: this.widthRotated ?? this.width,
          height: this.heightRotated ?? this.height,
          offsetX: (this.xRotated ?? this.x) - this.x,
          offsetY: (this.yRotated ?? this.y) - (this.y + this.height),
        },
      });
    }
  }

  private transformSpriteOnLoad(transform: SpriteTransform): void {
    this.sprite.addEventListener(
      'load',
      () => {
        const transformedUrl = getTransformedImageUrl(
          this.src,
          this.sprite,
          this.width,
          this.height,
          transform,
        );
        if (transformedUrl) {
          this.sprite.src = transformedUrl;
        }
      },
      { once: true },
    );
  }
}
