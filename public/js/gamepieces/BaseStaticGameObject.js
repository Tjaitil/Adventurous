import viewport from "../clientScripts/viewport.js";
export class BaseStaticGameObject {
    type;
    sprite;
    src;
    drawX;
    drawY;
    visible;
    diameterUp;
    diameterLeft;
    diameterRight;
    diameterDown;
    x;
    y;
    width;
    height;
    noCollision;
    id;
    constructor(initObjectData) {
        this.visible = initObjectData.visible;
        this.type = initObjectData.type;
        this.diameterUp = initObjectData.diameterUp;
        this.diameterRight = initObjectData.diameterRight;
        this.diameterDown = initObjectData.diameterDown;
        this.diameterLeft = initObjectData.diameterLeft;
        this.width = initObjectData.width;
        this.height = initObjectData.height;
        this.x = initObjectData.x;
        this.y = initObjectData.y;
        this.src = initObjectData.src;
        this.id = initObjectData.id;
        this.drawX = Math.round(initObjectData.x - viewport.offsetX);
        this.drawY = Math.round(initObjectData.y - viewport.offsetY);
        this.noCollision = initObjectData.noCollision;
        this.sprite = new Image(this.width, this.height);
        this.sprite.src = "public/images/" + this.src;
        // check source for missing format
        if (this.sprite.src.includes(".png") === false)
            this.sprite.src += ".png";
    }
}
