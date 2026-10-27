import { Game } from '../advclient';
import { GamePieces } from '../clientScripts/gamePieces';
import viewport from '../clientScripts/viewport';
import { ajaxP } from '../ajax';
import { updateInventory } from '../clientScripts/inventory';
import type { ItemSprite } from '../types/ItemSprite';
import { makeSprite } from '../clientScripts/spritesContainer';

export class Item {
  x: number;
  y: number;
  drawX: number;
  drawY: number;
  id: number;
  spriteObject: ItemSprite;
  animTime = 0;
  BOB_PERIOD = 0.9;
  BOB_MIN = 1;
  BOB_HEIGHT = 5;
  name: string;
  src: string;
  FRAME_SIZE = 32;
  width = 24;
  height = 24;
  checking = false;
  diameterDown: number;
  diameterUp: number;
  diameterLeft: number;
  diameterRight: number;

  constructor(drawX: number, drawY: number, src: string) {
    this.drawX = drawX;
    this.drawY = drawY;
    this.x = drawX + viewport.offsetX;
    this.y = drawY + viewport.offsetY;
    this.id = GamePieces.items.length + 1;
    this.diameterDown = this.drawY + this.height;
    this.diameterUp = this.drawY;
    this.diameterLeft = this.drawX;
    this.diameterRight = this.drawX + this.width;
    this.src = src;
    this.name = src;
    this.spriteObject = makeSprite(
      this.name,
      this.width,
      this.height,
      this.src,
    );
  }

  /**
   * Position in the bob cycle, 0 → 1 → 0. Eases smoothly, slowing near each
   * end and moving fastest in the middle. Shared by the item and its shadow so
   * they stay in sync.
   *
   * 1. cycles = animTime / BOB_PERIOD: how many full bobs have elapsed.
   * 2. angle = cycles * 2π: one full bob is one lap around a circle.
   * 3. cos(angle) goes 1 → -1 → 1 over one lap.
   * 4. (1 - cos) / 2 rescales that to 0 → 1 → 0, starting at 0.
   */
  getBobProgress(): number {
    const angle = (this.animTime / this.BOB_PERIOD) * Math.PI * 2;
    return (1 - Math.cos(angle)) / 2;
  }

  getBobOffset(): number {
    return this.BOB_MIN + this.BOB_HEIGHT * this.getBobProgress();
  }

  draw() {
    viewport.drawWorldSprite(
      this.spriteObject.image,
      0,
      0,
      this.FRAME_SIZE,
      this.FRAME_SIZE,
      this.drawX -
        GamePieces.player.xMovement +
        (this.FRAME_SIZE - this.width) / 2,
      this.drawY -
        GamePieces.player.yMovement +
        (this.FRAME_SIZE - this.height) / 2 +
        this.getBobOffset(),
      this.width,
      this.height,
    );
    if (!this.checking) this.pickUpItem();
    // this.shadow.draw();
    this.animTime += Game.properties.delta;
  }

  pickUpItem() {
    if (
      Math.abs(this.x - GamePieces.player.xpos) <= 10 &&
      Math.abs(this.y - GamePieces.player.ypos) <= 10
    ) {
      this.checking = true;
      let inInventory = false;
      if (
        document.getElementById('inventory').querySelectorAll('.inventory_item')
          .length === 18
      ) {
        const array = document
          .getElementById('inventory')
          .querySelectorAll('.inventory_item');
        for (
          let i = 0;
          i <
          document
            .getElementById('inventory')
            .querySelectorAll('.inventory_item').length;
          i++
        ) {
          if (array[i].innerHTML.indexOf(this.spriteObject.name) !== -1) {
            inInventory = true;
          }
        }
      }
      // if(inInventory === false) {
      // gameLogger.addMessage("ERROR: You don't have any free inventory spaces");
      // gameLogger.logMessages();
      //     return false;
      // }
      // else if(inInventory === true) {

      const data =
        'model=Loot' + '&method=addLoot' + '&item=' + this.spriteObject.name;
      GamePieces.items = GamePieces.items.filter((item: Item) => {
        return item.id != this.id;
      });
      ajaxP(data, function (response) {
        if (response[0] !== false) {
          updateInventory();
        }
      });
      // }
    }
  }
}

/**
 * TODO: Figure out if we should keep this
 */
export class ItemShadowAnimation {
  x: number;
  y: number;
  drawX: number;
  drawY: number;
  spriteObject: ItemSprite;
  FRAME_COUNT = 3;
  SIZE = 56;
  OFFSET_Y = 0;

  /**
   * @param itemSize Drawn width of the item. The shadow is centred on it.
   */
  constructor(x: number, y: number, itemSize: number) {
    this.x = x;
    this.y = y;
    this.drawX = this.x + (itemSize - this.SIZE) / 2;
    this.drawY = this.y + this.OFFSET_Y;
    this.spriteObject = makeSprite('shadow', 96, 32, 'shadow sprite');
  }

  /**
   * @param bobProgress 0 → 1 from Item.getBobProgress(). Picks the shadow
   * frame: smallest at 0, largest at 1.
   */
  draw(bobProgress: number) {
    const frame = Math.round(bobProgress * (this.FRAME_COUNT - 1));
    viewport.drawWorldSprite(
      this.spriteObject.image,
      frame * 32,
      0,
      32,
      32,
      this.drawX - GamePieces.player.xMovement,
      this.drawY - GamePieces.player.yMovement,
      this.SIZE,
      this.SIZE,
    );
  }
}
