import { ItemSprite } from "../types/ItemSprite";
import { AssetPaths } from "./ImagePath";

export function makeSprite(name: string, width: number, height: number, src: string) {
        let spriteObject: ItemSprite;
        let image = new Image(width, height);
        image.src = AssetPaths.getImagePath(src + ".png");
        image.onload = () => {
            spriteObject.name = name;
            spriteObject.image = image;
            spriteObject.width = width;
            spriteObject.height = height;
        };
        image.onerror = () => {
            console.log(image.src + " Sprite loading failed");
        };
        return spriteObject;
    }
