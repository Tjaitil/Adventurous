import { describe, expect, test, beforeEach, vi } from 'vitest';
import { MovingGameObject } from '@/gamepieces/MovingGameObject';
import type { SpatialGrid, SpatialObject } from '@/clientScripts/SpatialGrid';

vi.mock('@/clientScripts/controls', () => ({
  controls: {
    playerLeft: false,
    playerRight: false,
    playerUp: false,
    playerDown: false,
  },
}));

import { controls } from '@/clientScripts/controls';

class TestMovingObject extends MovingGameObject {
  id = 1;
  type = 'daqloon';
  src = '';
  sprite = new Image();
  x = 0;
  y = 0;
  width = 10;
  height = 10;
  drawX = 0;
  drawY = 0;
  visible = true;
  noCollision = false;
  diameterUp = 0;
  diameterRight = 10;
  diameterDown = 10;
  diameterLeft = 0;
  up: MovingGameObject['up'] = '';
  right: MovingGameObject['right'] = '';
  down: MovingGameObject['down'] = '';
  left: MovingGameObject['left'] = '';
  speedX = 0;
  speedY = 0;
  movementSpeed = 1;
  currentAnimation = 'idle';
  animTimer = 0;
}

function makeGrid(candidates: SpatialObject[]) {
  return { query: vi.fn(() => candidates) } as unknown as SpatialGrid<SpatialObject>;
}

function makeCandidate(overrides: Partial<SpatialObject> = {}): SpatialObject {
  return {
    diameterLeft: 0,
    diameterUp: 0,
    diameterRight: 0,
    diameterDown: 0,
    noCollision: false,
    ...overrides,
  };
}

describe('MovingGameObject.collisionCheck', () => {
  let obj: TestMovingObject;

  beforeEach(() => {
    obj = new TestMovingObject();
    obj.diameterLeft = 0;
    obj.diameterUp = 0;
    obj.diameterRight = 10;
    obj.diameterDown = 10;
    controls.playerLeft = false;
    controls.playerRight = false;
    controls.playerUp = false;
    controls.playerDown = false;
  });

  test('leaves all directions unblocked when there are no candidates', () => {
    obj.collisionCheck(makeGrid([]));

    expect(obj.up).toBe('');
    expect(obj.right).toBe('');
    expect(obj.down).toBe('');
    expect(obj.left).toBe('');
  });

  test('queries the spatial grid with the query margin applied around the object bounds', () => {
    const grid = makeGrid([]);

    obj.collisionCheck(grid);

    expect(grid.query).toHaveBeenCalledWith(
      obj.diameterLeft - MovingGameObject.QUERY_MARGIN,
      obj.diameterUp - MovingGameObject.QUERY_MARGIN,
      obj.diameterRight + MovingGameObject.QUERY_MARGIN,
      obj.diameterDown + MovingGameObject.QUERY_MARGIN,
    );
  });

  test('blocks down when a candidate touches the bottom edge within tolerance', () => {
    // Candidate's top edge sits just below the object's bottom edge, within TOUCH_TOLERANCE.
    const candidate = makeCandidate({
      diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE,
      diameterLeft: 0,
      diameterRight: 10,
      diameterDown: 20,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.down).toBe('blocked');
    expect(obj.up).toBe('');
    expect(obj.left).toBe('');
    expect(obj.right).toBe('');
  });

  test('blocks right when a candidate touches the right edge within tolerance', () => {
    const candidate = makeCandidate({
      diameterLeft: obj.diameterRight + MovingGameObject.TOUCH_TOLERANCE,
      diameterUp: 0,
      diameterDown: 10,
      diameterRight: 20,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.right).toBe('blocked');
  });

  test('blocks up when a candidate touches the top edge within tolerance', () => {
    const candidate = makeCandidate({
      diameterDown: obj.diameterUp - MovingGameObject.TOUCH_TOLERANCE,
      diameterLeft: 0,
      diameterRight: 10,
      diameterUp: -10,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.up).toBe('blocked');
  });

  test('blocks left when a candidate touches the left edge within tolerance', () => {
    const candidate = makeCandidate({
      diameterRight: obj.diameterLeft - MovingGameObject.TOUCH_TOLERANCE,
      diameterUp: 0,
      diameterDown: 10,
      diameterLeft: -10,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.left).toBe('blocked');
  });

  test('does not block when a candidate is further away than the touch tolerance', () => {
    const candidate = makeCandidate({
      diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE + 1,
      diameterLeft: 0,
      diameterRight: 10,
      diameterDown: 20,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.down).toBe('');
  });

  test('ignores candidates flagged with noCollision', () => {
    const candidate = makeCandidate({
      diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE,
      diameterLeft: 0,
      diameterRight: 10,
      diameterDown: 20,
      noCollision: true,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.down).toBe('');
  });

  test('does not block down when the touching candidate does not overlap horizontally', () => {
    const candidate = makeCandidate({
      diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE,
      diameterLeft: 1000,
      diameterRight: 1010,
      diameterDown: 1020,
    });

    obj.collisionCheck(makeGrid([candidate]));

    expect(obj.down).toBe('');
  });

  test('resets previously blocked directions at the start of each call', () => {
    const blockingBelow = makeCandidate({
      diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE,
      diameterLeft: 0,
      diameterRight: 10,
      diameterDown: 20,
    });
    obj.collisionCheck(makeGrid([blockingBelow]));
    expect(obj.down).toBe('blocked');

    obj.collisionCheck(makeGrid([]));

    expect(obj.down).toBe('');
  });

  describe('non-player objects', () => {
    test('zeroes speedX when moving left into a blocked left side', () => {
      const candidate = makeCandidate({
        diameterRight: obj.diameterLeft - MovingGameObject.TOUCH_TOLERANCE,
        diameterUp: 0,
        diameterDown: 10,
        diameterLeft: -10,
      });
      obj.speedX = -5;

      obj.collisionCheck(makeGrid([candidate]));

      expect(obj.speedX).toBe(0);
    });

    test('zeroes speedY when moving down into a blocked bottom side', () => {
      const candidate = makeCandidate({
        diameterUp: obj.diameterDown + MovingGameObject.TOUCH_TOLERANCE,
        diameterLeft: 0,
        diameterRight: 10,
        diameterDown: 20,
      });
      obj.speedY = 5;

      obj.collisionCheck(makeGrid([candidate]));

      expect(obj.speedY).toBe(0);
    });

    test('leaves speed untouched when moving away from the blocked side', () => {
      const candidate = makeCandidate({
        diameterRight: obj.diameterLeft - MovingGameObject.TOUCH_TOLERANCE,
        diameterUp: 0,
        diameterDown: 10,
        diameterLeft: -10,
      });
      obj.speedX = 5; // moving right, away from the blocked left side

      obj.collisionCheck(makeGrid([candidate]));

      expect(obj.speedX).toBe(5);
    });
  });

  describe('player objects', () => {
    beforeEach(() => {
      obj.type = 'Player';
    });

    test('zeroes speedX when controls.playerLeft is held and left is blocked', () => {
      const candidate = makeCandidate({
        diameterRight: obj.diameterLeft - MovingGameObject.TOUCH_TOLERANCE,
        diameterUp: 0,
        diameterDown: 10,
        diameterLeft: -10,
      });
      controls.playerLeft = true;
      obj.speedX = -5;

      obj.collisionCheck(makeGrid([candidate]));

      expect(obj.speedX).toBe(0);
    });

    test('does not zero speedX when left is blocked but the player is not pressing left', () => {
      const candidate = makeCandidate({
        diameterRight: obj.diameterLeft - MovingGameObject.TOUCH_TOLERANCE,
        diameterUp: 0,
        diameterDown: 10,
        diameterLeft: -10,
      });
      controls.playerLeft = false;
      obj.speedX = -5;

      obj.collisionCheck(makeGrid([candidate]));

      expect(obj.speedX).toBe(-5);
    });
  });
});
