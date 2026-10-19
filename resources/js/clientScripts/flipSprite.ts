const flippedCache = new Map<string, string>();

/**
 * Mirrors a loaded image once and returns it as a data URL.
 * Cached per key so objects sharing a src share the result.
 */
export function getFlippedImageUrl(
  key: string,
  image: HTMLImageElement,
  flipX: boolean,
  flipY: boolean,
): string | null {
  const cacheKey = `${key}|${flipX}|${flipY}`;
  const cached = flippedCache.get(cacheKey);
  if (cached) {
    return cached;
  }

  const canvas = document.createElement('canvas');
  canvas.width = image.naturalWidth;
  canvas.height = image.naturalHeight;
  const context = canvas.getContext('2d');
  if (!context) {
    return null;
  }

  context.translate(flipX ? canvas.width : 0, flipY ? canvas.height : 0);
  context.scale(flipX ? -1 : 1, flipY ? -1 : 1);
  context.drawImage(image, 0, 0);

  const url = canvas.toDataURL();
  flippedCache.set(cacheKey, url);

  return url;
}

export function clearFlippedImageCache(): void {
  flippedCache.clear();
}
