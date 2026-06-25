import type { ItemSprite } from '../types/ItemSprite';
import { AssetPaths } from './ImagePath';

export function makeSprite(
  name: string,
  width: number,
  height: number,
  src: string,
): ItemSprite {
  const image = new Image(width, height);
  const spriteObject: ItemSprite = { name, image, src: src || '', width, height };
  if (src) {
    image.src = AssetPaths.getImagePath(src + '.png');
  }
  image.onerror = () => {
    console.log(image.src + ' Sprite loading failed');
  };
  return spriteObject;
}
