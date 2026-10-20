import { beforeEach, describe, expect, test, vi } from 'vitest';
import type { StaticGameObject } from '@/types/gamepieces/GameObject';

vi.mock('@/clientScripts/viewport', () => ({
  default: { offsetX: 0, offsetY: 0 },
}));
vi.mock('@/clientScripts/gamePieces', () => ({ GamePieces: {} }));
vi.mock('@/clientScripts/spriteTransform', () => ({
  getTransformedImageUrl: vi.fn(() => 'data:image/png;base64,transformed'),
}));

import { BaseStaticGameObject } from '@/gamepieces/BaseStaticGameObject';
import { getTransformedImageUrl } from '@/clientScripts/spriteTransform';

function makeObject(overrides: Partial<StaticGameObject> = {}) {
  return {
    id: 1,
    src: 'pole.png',
    type: '',
    visible: true,
    noCollision: false,
    diameterUp: 0,
    diameterLeft: 0,
    diameterRight: 0,
    diameterDown: 0,
    x: 100,
    y: 50,
    width: 32,
    height: 32,
    gid: 263,
    ...overrides,
  } as StaticGameObject;
}

describe('BaseStaticGameObject sprite transform', () => {
  beforeEach(() => {
    vi.mocked(getTransformedImageUrl).mockClear();
  });

  test('swaps the sprite for the transformed image once loaded', () => {
    const object = new BaseStaticGameObject(
      makeObject({
        flipY: true,
        rotation: 180,
        xRotated: 68,
        yRotated: 82,
        widthRotated: 32,
        heightRotated: 32,
      }),
    );

    object.sprite.dispatchEvent(new Event('load'));

    expect(getTransformedImageUrl).toHaveBeenCalledWith(
      'pole.png',
      expect.anything(),
      32,
      32,
      {
        flipX: false,
        flipY: true,
        rotation: 180,
        box: { width: 32, height: 32, offsetX: -32, offsetY: 0 },
      },
    );
    expect(object.sprite.src).toBe('data:image/png;base64,transformed');
  });

  test('draws at the rotated box when set', () => {
    const object = new BaseStaticGameObject(
      makeObject({
        rotation: 90,
        xRotated: 60,
        yRotated: 20,
        widthRotated: 40,
        heightRotated: 24,
      }),
    );

    expect([object.drawX, object.drawY]).toEqual([60, 20]);
    expect([object.x, object.y, object.width, object.height]).toEqual([
      100, 50, 32, 32,
    ]);
    expect(object.sprite.width).toBe(40);
    expect(object.sprite.height).toBe(24);
  });

  test('draws at x/y when not rotated', () => {
    const object = new BaseStaticGameObject(makeObject());

    expect([object.drawX, object.drawY]).toEqual([100, 50]);
    expect(object.widthRotated).toBeNull();
  });

  test('transforms only once when the swapped image loads', () => {
    const object = new BaseStaticGameObject(makeObject({ rotation: 90 }));

    object.sprite.dispatchEvent(new Event('load'));
    object.sprite.dispatchEvent(new Event('load'));

    expect(getTransformedImageUrl).toHaveBeenCalledTimes(1);
  });

  test('keeps the plain image without flips or rotation', () => {
    const object = new BaseStaticGameObject(makeObject({ rotation: 0 }));

    object.sprite.dispatchEvent(new Event('load'));

    expect(getTransformedImageUrl).not.toHaveBeenCalled();
  });

  test('ignores objects without a gid', () => {
    const object = new BaseStaticGameObject(
      makeObject({ gid: undefined, rotation: 90 }),
    );

    object.sprite.dispatchEvent(new Event('load'));

    expect(getTransformedImageUrl).not.toHaveBeenCalled();
  });
});
