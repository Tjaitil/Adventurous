/** Rotated box of the sprite, as sent by the world loader */
export type SpriteBox = {
  width: number;
  height: number;
  /** Top-left of the rotated box relative to the bottom-left pivot */
  offsetX: number;
  offsetY: number;
};

export type SpriteTransform = {
  flipX: boolean;
  flipY: boolean;
  /** Clockwise degrees around the bottom-left corner */
  rotation: number;
  box: SpriteBox;
};

const transformedCache = new Map<string, string>();

/**
 * Flips an image inside its own box, then rotates it like Tiled does, and
 * returns the result as a data URL. Cached so objects sharing a src share it.
 */
export const getTransformedImageUrl = (
  key: string,
  image: HTMLImageElement,
  width: number,
  height: number,
  { flipX, flipY, rotation, box }: SpriteTransform,
): string | null => {
  const cacheKey = `${key}|${width}x${height}|${flipX}|${flipY}|${rotation}`;
  const cached = transformedCache.get(cacheKey);
  if (cached) {
    return cached;
  }

  const canvas = document.createElement('canvas');
  canvas.width = Math.ceil(box.width);
  canvas.height = Math.ceil(box.height);
  const context = canvas.getContext('2d');
  if (!context) {
    return null;
  }

  context.translate(-box.offsetX, -box.offsetY);
  context.rotate((rotation * Math.PI) / 180);
  context.translate(0, -height);
  if (flipX) {
    context.translate(width, 0);
    context.scale(-1, 1);
  }
  if (flipY) {
    context.translate(0, height);
    context.scale(1, -1);
  }
  context.drawImage(image, 0, 0, width, height);

  const url = canvas.toDataURL();
  transformedCache.set(cacheKey, url);

  return url;
};

export const clearTransformedImageCache = (): void => {
  transformedCache.clear();
};
