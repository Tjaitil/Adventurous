import { Game } from "../advclient.js";
import { loadSprite } from "../clientScripts/spritesContainer.js";
import { GamePieces } from "../clientScripts/gamePieces.js";
import viewport from "../clientScripts/viewport.js";
import { ajaxP } from "../ajax.js";
import { updateInventory } from "../clientScripts/inventory.js";
import { ItemSprite } from "../types/itemSprite.js";

export class Item {
    x: number;
    y: number;
    drawX: number;
    drawY: number;
    id: number;
    spriteObject: ItemSprite;
    shadow: ItemShadowAnimation;
    name: string;
    width = 32;
    height = 32;
    scale = 0.6;
    loopIndex = 0;
    loopArray = [1, 4, 6, 4];
    checking: boolean;
    src: string;

    constructor(drawX: number, drawY: number, src: string) {
        this.drawX = drawX;
        this.drawY = drawY;
        this.x = drawX + viewport.offsetX;
        this.y = drawY + viewport.offsetY;
        this.id = GamePieces.items.length + 1;

        this.spriteObject = loadSprite(this.name, this.width, this.height, this.src);
        this.shadow = new ItemShadowAnimation(this.x, this.y);
    }

    draw() {
        if (this.loopIndex > 3) this.loopIndex = 0;

        viewport.drawSprite(
            this.spriteObject.image,
            0,
            0,
            32 * viewport.scale,
            32 * viewport.scale,
            this.drawX - GamePieces.player.xMovement * viewport.scale + 5,
            this.drawY - GamePieces.player.yMovement * viewport.scale + this.loopArray[this.loopIndex],
            this.width * this.scale,
            this.height * this.scale
        );
        this.shadow.draw();
        if (this.checking === false) this.pickUpItem();
        if (Game.properties.duration % 20 === 0) this.loopIndex++;
    }

    pickUpItem() {
        if (Math.abs(this.x - GamePieces.player.xpos) <= 10 && Math.abs(this.y - GamePieces.player.ypos) <= 10) {
            this.checking = true;
            let inInventory = false;
            if (document.getElementById("inventory").querySelectorAll(".inventory_item").length === 18) {
                let array = document.getElementById("inventory").querySelectorAll(".inventory_item");
                for (
                    let i = 0;
                    i < document.getElementById("inventory").querySelectorAll(".inventory_item").length;
                    i++
                ) {
                    if (array[i].innerHTML.indexOf(this.spriteObject.name) !== -1) {
                        inInventory = true;
                    }
                }
            }
            // if(inInventory === false) {
            // gameLogger.addMessage("ERROR: You don't have any free inventory spaces");
            // gameLogger.logMessages();
            //     return false;
            // }
            // else if(inInventory === true) {

            let data = "model=Loot" + "&method=addLoot" + "&item=" + this.spriteObject.name;
            GamePieces.items = GamePieces.items.filter((item: Item) => {
                return item.id != this.id;
            });
            ajaxP(data, function (response) {
                if (response[0] !== false) {
                    updateInventory();
                }
            });
            // }
        }
    }
}

export class ItemShadowAnimation {
    x: number;
    y: number;
    drawX: number;
    drawY: number;
    indexX = 0;
    spriteObject: ItemSprite;
    height: number;
    width: number;
    loopArray = [0, 1, 2, 1];

    constructor(x: number, y: number) {
        this.x = x;
        this.y = y;
        this.drawX = this.x;
        this.drawY = this.y;
    }

    draw() {
        if (this.indexX > 3) this.indexX = 0;
        viewport.drawSprite(
            this.spriteObject.image,
            this.loopArray[this.indexX] * 32,
            0,
            32 * viewport.scale,
            32 * viewport.scale,
            this.drawX - GamePieces.player.xMovement * viewport.scale - 8,
            this.drawY - GamePieces.player.yMovement * viewport.scale - 8,
            48,
            48
        );
        if (Game.properties.duration % 20 === 0) {
            this.indexX++;
        }
    }
}
