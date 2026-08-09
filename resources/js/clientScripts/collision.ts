import type { DirectionBlockedCheck } from '../types/gamepieces/MovingGameObject';
import { controls } from './controls';
import { GamePieces } from './gamePieces';

export abstract class CollidableGamePiece {
  abstract type: string;
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

  collisionCheck(debug = false) {
    // Collision detection, if user is less than 1px from object prevent movement

    this.down = '';
    this.right = '';
    this.up = '';
    this.left = '';

    const candidates = GamePieces.spatialGrid.query(
      this.diameterLeft - 10,
      this.diameterUp - 10,
      this.diameterRight + 10,
      this.diameterDown + 10,
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
        Math.abs(this.diameterDown - candidates[i].diameterUp) <= 2 &&
        this.diameterRight >= candidates[i].diameterLeft &&
        this.diameterLeft <= candidates[i].diameterRight
      ) {
        this.down = 'blocked';
        if (debug) {
          console.log(candidates[i]);
          console.log('player_down');
        }
      }
      if (
        Math.abs(this.diameterRight - candidates[i].diameterLeft) <= 2 &&
        this.diameterUp <= candidates[i].diameterDown &&
        this.diameterDown >= candidates[i].diameterUp
      ) {
        this.right = 'blocked';
        if (debug) {
          console.log(candidates[i]);
          console.log('player right');
        }
      }
      if (
        Math.abs(this.diameterUp - candidates[i].diameterDown) <= 2 &&
        this.diameterRight >= candidates[i].diameterLeft &&
        this.diameterLeft <= candidates[i].diameterRight
      ) {
        this.up = 'blocked';
        if (debug) {
          console.log(candidates[i]);
          console.log('player up');
        }
      }
      if (
        Math.abs(this.diameterLeft - candidates[i].diameterRight) <= 2 &&
        this.diameterUp <= candidates[i].diameterDown &&
        this.diameterDown >= candidates[i].diameterUp
      ) {
        this.left = 'blocked';
        if (debug) {
          console.log(candidates[i]);
          console.log('player left');
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
