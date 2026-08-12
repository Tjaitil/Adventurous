import { controls } from '../clientScripts/controls';
import type { SpatialGrid, SpatialObject } from '../clientScripts/SpatialGrid';
import type { GameObject } from '../types/gamepieces/GameObject';

export type DirectionBlockedCheck = 'blocked' | '';

// `type` is re-declared as a plain string below rather than inherited from
// GameObject: Player/Daqloon use values ('player', 'daqloon') outside
// GameObjectType's union, so it can't be narrowed to that type here.
export abstract class MovingGameObject implements Omit<GameObject, 'type'> {
  // How close two edges must be to count as touching. Movement happens in
  // discrete per-frame steps rather than continuous swept collision, so
  // this needs to be >= a frame's worth of movement or a piece can jump
  // straight from "not touching" to "overlapping" without ever blocking.
  static readonly TOUCH_TOLERANCE = 2;
  // Must stay >= TOUCH_TOLERANCE, or a nearly-touching object would be
  // excluded from the query before the touch check ever runs against it.
  static readonly QUERY_MARGIN = 3;

  abstract id: number;
  abstract type: string;
  abstract src: string;
  abstract sprite: HTMLImageElement;
  abstract x: number;
  abstract y: number;
  abstract width: number;
  abstract height: number;
  abstract drawX: number;
  abstract drawY: number;
  abstract visible: boolean;
  abstract noCollision: boolean;
  abstract diameterUp: number;
  abstract diameterRight: number;
  abstract diameterDown: number;
  abstract diameterLeft: number;
  abstract up: DirectionBlockedCheck;
  abstract right: DirectionBlockedCheck;
  abstract down: DirectionBlockedCheck;
  abstract left: DirectionBlockedCheck;
  abstract speedX: number;
  abstract speedY: number;
  abstract movementSpeed: number;
  abstract currentAnimation: string;
  abstract animTimer: number;

  collisionCheck(spatialGrid: SpatialGrid<SpatialObject>, debug = false) {
    this.down = '';
    this.right = '';
    this.up = '';
    this.left = '';

    const candidates = spatialGrid.query(
      this.diameterLeft - MovingGameObject.QUERY_MARGIN,
      this.diameterUp - MovingGameObject.QUERY_MARGIN,
      this.diameterRight + MovingGameObject.QUERY_MARGIN,
      this.diameterDown + MovingGameObject.QUERY_MARGIN,
    );

    for (let i = 0, n = candidates.length; i < n; i++) {
      if (candidates[i].noCollision) {
        continue;
      }

      // If all directions is blocked break loop
      if (
        this.up === 'blocked' &&
        this.right === 'blocked' &&
        this.down === 'blocked' &&
        this.left === 'blocked'
      ) {
        break;
      }
      if (
        Math.abs(this.diameterDown - candidates[i].diameterUp) <=
          MovingGameObject.TOUCH_TOLERANCE &&
        this.diameterRight >= candidates[i].diameterLeft &&
        this.diameterLeft <= candidates[i].diameterRight
      ) {
        this.down = 'blocked';
        if (debug) {
          console.log(candidates[i], 'down');
        }
      }
      if (
        Math.abs(this.diameterRight - candidates[i].diameterLeft) <=
          MovingGameObject.TOUCH_TOLERANCE &&
        this.diameterUp <= candidates[i].diameterDown &&
        this.diameterDown >= candidates[i].diameterUp
      ) {
        this.right = 'blocked';
        if (debug) {
          console.log(candidates[i], 'right');
        }
      }
      if (
        Math.abs(this.diameterUp - candidates[i].diameterDown) <=
          MovingGameObject.TOUCH_TOLERANCE &&
        this.diameterRight >= candidates[i].diameterLeft &&
        this.diameterLeft <= candidates[i].diameterRight
      ) {
        this.up = 'blocked';
        if (debug) {
          console.log(candidates[i], 'up');
        }
      }
      if (
        Math.abs(this.diameterLeft - candidates[i].diameterRight) <=
          MovingGameObject.TOUCH_TOLERANCE &&
        this.diameterUp <= candidates[i].diameterDown &&
        this.diameterDown >= candidates[i].diameterUp
      ) {
        this.left = 'blocked';
        if (debug) {
          console.log(candidates[i], 'left');
        }
      }
    }

    if (this.type === 'Player') {
      if (controls.playerLeft && this.left == 'blocked') {
        this.speedX = 0;
      }
      if (controls.playerRight && this.right == 'blocked') {
        this.speedX = 0;
      }
      if (controls.playerDown && this.down == 'blocked') {
        this.speedY = 0;
      }
      if (controls.playerUp && this.up == 'blocked') {
        this.speedY = 0;
      }
    } else {
      if (this.speedX < 0 && this.left == 'blocked') {
        this.speedX = 0;
      }
      if (this.speedX > 0 && this.right == 'blocked') {
        this.speedX = 0;
      }
      if (this.speedY > 0 && this.down == 'blocked') {
        this.speedY = 0;
      }
      if (this.speedY < 0 && this.up == 'blocked') {
        this.speedY = 0;
      }
    }
  }
}
