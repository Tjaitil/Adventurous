import { beforeEach, describe, expect, test, vi } from 'vitest';
import {
  clearTransformedImageCache,
  getTransformedImageUrl,
} from '@/engine/spriteTransform';
import type { SpriteTransform } from '@/engine/spriteTransform';

function mockCanvas() {
  const context = {
    translate: vi.fn<(x: number, y: number) => void>(),
    rotate: vi.fn(),
    scale: vi.fn(),
    drawImage: vi.fn(),
  };
  const canvas = {
    width: 0,
    height: 0,
    getContext: vi.fn(() => context),
    toDataURL: vi.fn(() => 'data:image/png;base64,transformed'),
  };
  vi.spyOn(document, 'createElement').mockReturnValue(
    canvas as unknown as HTMLCanvasElement,
  );

  return { canvas, context };
}

const image = {} as HTMLImageElement;
const noTransform: SpriteTransform = {
  flipX: false,
  flipY: false,
  rotation: 0,
  box: { width: 32, height: 32, offsetX: 0, offsetY: -32 },
};

describe('getTransformedImageUrl', () => {
  beforeEach(() => {
    clearTransformedImageCache();
    vi.restoreAllMocks();
  });

  test('sizes the canvas to the rotated box', () => {
    const { canvas } = mockCanvas();

    getTransformedImageUrl('a.png', image, 40, 60, {
      ...noTransform,
      rotation: 90,
      box: { width: 59.5, height: 40, offsetX: 0, offsetY: 0 },
    });

    expect(canvas.width).toBe(60);
    expect(canvas.height).toBe(40);
  });

  test('moves the rotated box to the canvas origin', () => {
    const { context } = mockCanvas();

    getTransformedImageUrl('a.png', image, 40, 60, {
      ...noTransform,
      rotation: 180,
      box: { width: 40, height: 60, offsetX: -40, offsetY: 0 },
    });

    expect(context.translate).toHaveBeenNthCalledWith(1, 40, -0);
  });

  test('flips first, then rotates around the pivot', () => {
    const { context } = mockCanvas();

    getTransformedImageUrl('pole.png', image, 32, 32, {
      flipX: false,
      flipY: true,
      rotation: 180,
      box: { width: 32, height: 32, offsetX: -32, offsetY: 0 },
    });

    const translations = context.translate.mock.calls.map(([x, y]) => [
      x + 0,
      y + 0,
    ]);
    expect(translations).toEqual([
      [32, 0],
      [0, -32],
      [0, 32],
    ]);
    expect(context.rotate).toHaveBeenCalledWith(Math.PI);
    expect(context.scale).toHaveBeenCalledWith(1, -1);
    expect(context.drawImage).toHaveBeenCalledWith(image, 0, 0, 32, 32);
  });

  test('mirrors horizontally', () => {
    const { context } = mockCanvas();

    getTransformedImageUrl('a.png', image, 32, 16, {
      ...noTransform,
      flipX: true,
    });

    expect(context.translate).toHaveBeenCalledWith(32, 0);
    expect(context.scale).toHaveBeenCalledWith(-1, 1);
  });

  test('caches per key, size, flips and rotation', () => {
    const { canvas } = mockCanvas();

    getTransformedImageUrl('a.png', image, 32, 32, noTransform);
    getTransformedImageUrl('a.png', image, 32, 32, noTransform);
    getTransformedImageUrl('a.png', image, 32, 32, {
      ...noTransform,
      rotation: 90,
    });

    expect(canvas.toDataURL).toHaveBeenCalledTimes(2);
  });

  test('returns null without a 2d context', () => {
    const { canvas } = mockCanvas();
    canvas.getContext.mockReturnValue(null as never);

    expect(
      getTransformedImageUrl('a.png', image, 32, 32, noTransform),
    ).toBeNull();
  });
});
