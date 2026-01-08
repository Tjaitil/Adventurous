import { Player } from './../gamepieces/Player';
import type { MovingGameObjectTypes } from '../types/gamepieces/MovingGameObject';
import { controls } from './controls';
import { GamePieces } from './gamePieces';

export function collisionCheck(
  gamePiece: MovingGameObjectTypes,
  debug = false,
) {
  // Collision detection, if user is less than 1px from object prevent movement

  gamePiece.down = '';
  gamePiece.right = '';
  gamePiece.down = '';
  gamePiece.left = '';

  for (let i = 0, n = GamePieces.nearObjects.length; i < n; i++) {
    if (GamePieces.nearObjects[i].noCollision) {
      continue;
    }

    // If all directions is blocked break loop
    if (
      gamePiece.up === 'blocked' &&
      gamePiece.right === 'blocked' &&
      gamePiece.down === 'blocked' &&
      gamePiece.left === 'blocked'
    ) {
      break;
    }
    if (
      Math.abs(gamePiece.diameterDown - GamePieces.nearObjects[i].diameterUp) <=
        2 &&
      gamePiece.diameterRight >= GamePieces.nearObjects[i].diameterLeft &&
      gamePiece.diameterLeft <= GamePieces.nearObjects[i].diameterRight
    ) {
      gamePiece.down = 'blocked';
      if (debug) {
        console.log(GamePieces.nearObjects[i]);
        console.log('player_down');
      }
    }
    if (
      Math.abs(
        gamePiece.diameterRight - GamePieces.nearObjects[i].diameterLeft,
      ) <= 2 &&
      gamePiece.diameterUp <= GamePieces.nearObjects[i].diameterDown &&
      gamePiece.diameterDown >= GamePieces.nearObjects[i].diameterUp
    ) {
      gamePiece.right = 'blocked';
      if (debug) {
        console.log(GamePieces.nearObjects[i]);
        console.log('player right');
      }
    }
    if (
      Math.abs(gamePiece.diameterUp - GamePieces.nearObjects[i].diameterDown) <=
        2 &&
      gamePiece.diameterRight >= GamePieces.nearObjects[i].diameterLeft &&
      gamePiece.diameterLeft <= GamePieces.nearObjects[i].diameterRight
    ) {
      gamePiece.up = 'blocked';
      if (debug) {
        console.log(GamePieces.nearObjects[i]);
        console.log('player up');
      }
    }
    if (
      Math.abs(
        gamePiece.diameterLeft - GamePieces.nearObjects[i].diameterRight,
      ) <= 2 &&
      gamePiece.diameterUp <= GamePieces.nearObjects[i].diameterDown &&
      gamePiece.diameterDown >= GamePieces.nearObjects[i].diameterUp
    ) {
      gamePiece.left = 'blocked';
      if (debug) {
        console.log(GamePieces.nearObjects[i]);
        console.log('player left');
      }
    }
  }

  if (gamePiece.type === 'Player') {
    if (controls.playerLeft && gamePiece.left == 'blocked') {
      gamePiece.speedX = 0;
    }
    if (controls.playerRight && gamePiece.right == 'blocked') {
      gamePiece.speedX = 0;
    }
    if (controls.playerDown && gamePiece.down == 'blocked') {
      gamePiece.speedY = 0;
    }
    if (controls.playerUp && gamePiece.up == 'blocked') {
      gamePiece.speedY = 0;
    }
  } else {
    if (gamePiece.speedX < 0 && gamePiece.left == 'blocked') {
      gamePiece.speedX = 0;
    }
    if (gamePiece.speedX > 0 && gamePiece.right == 'blocked') {
      gamePiece.speedX = 0;
    }
    if (gamePiece.speedY > 0 && gamePiece.down == 'blocked') {
      gamePiece.speedY = 0;
    }
    if (gamePiece.speedY < 0 && gamePiece.up == 'blocked') {
      gamePiece.speedY = 0;
    }
  }
}
