import { beforeEach, describe, expect, test, vi } from 'vitest';
import {
  clearFlippedImageCache,
  getFlippedImageUrl,
} from '@/clientScripts/flipSprite';

function mockCanvas() {
  const context = {
    translate: vi.fn(),
    scale: vi.fn(),
    drawImage: vi.fn(),
  };
  const canvas = {
    width: 0,
    height: 0,
    getContext: vi.fn(() => context),
    toDataURL: vi.fn(() => 'data:image/png;base64,flipped'),
  };
  vi.spyOn(document, 'createElement').mockReturnValue(
    canvas as unknown as HTMLCanvasElement,
  );

  return { canvas, context };
}

const image = { naturalWidth: 32, naturalHeight: 16 } as HTMLImageElement;

describe('getFlippedImageUrl', () => {
  beforeEach(() => {
    clearFlippedImageCache();
    vi.restoreAllMocks();
  });

  test('mirrors horizontally', () => {
    const { context } = mockCanvas();

    const url = getFlippedImageUrl('pole.png', image, true, false);

    expect(url).toBe('data:image/png;base64,flipped');
    expect(context.translate).toHaveBeenCalledWith(32, 0);
    expect(context.scale).toHaveBeenCalledWith(-1, 1);
  });

  test('mirrors vertically', () => {
    const { context } = mockCanvas();

    getFlippedImageUrl('pole.png', image, false, true);

    expect(context.translate).toHaveBeenCalledWith(0, 16);
    expect(context.scale).toHaveBeenCalledWith(1, -1);
  });

  test('caches per key and flip combination', () => {
    const { canvas } = mockCanvas();

    getFlippedImageUrl('pole.png', image, true, false);
    getFlippedImageUrl('pole.png', image, true, false);
    getFlippedImageUrl('pole.png', image, false, true);

    expect(canvas.toDataURL).toHaveBeenCalledTimes(2);
  });

  test('returns null without a 2d context', () => {
    const { canvas } = mockCanvas();
    canvas.getContext.mockReturnValue(null as never);

    expect(getFlippedImageUrl('pole.png', image, true, false)).toBeNull();
  });
});
