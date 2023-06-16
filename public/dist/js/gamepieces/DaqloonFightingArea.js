import { Daqloon } from "./../gamepieces/Daqloon.js";
import { getRandomInteger } from "../utilities/getRandomInteger.js";
import { GamePieces } from '../clientScripts/gamePieces.js';
export class DaqloonFightingArea {
    daqloon_amount;
    daqloons = [];
    diameterUp;
    diameterLeft;
    diameterDown;
    diameterRight;
    width;
    height;
    x;
    y;
    noCollision = true;
    type;
    constructor(initData) {
        // Initalize data
        this.width = initData.width;
        this.height = initData.height;
        this.x = initData.x;
        this.y = initData.y;
        this.daqloon_amount = initData.daqloon_amount;
        this.diameterUp = this.y;
        this.diameterLeft = this.x;
        this.diameterDown = this.y + this.height;
        this.diameterRight = this.x + this.width;
    }
    loadDaqloons() {
        this.daqloons.push(new Daqloon(0, getRandomInteger(this.diameterUp, this.diameterDown - 32), getRandomInteger(this.diameterUp, this.diameterDown - 32), {
            diameterUp: this.diameterUp,
            diameterRight: this.diameterRight,
            diameterDown: this.diameterDown,
            diameterLeft: this.diameterLeft,
        }));
        // let daqloons = [];
        // let y;
        // let x;
        // for (let i = 0; i < this.daqloon_amount; i++) {
        //     y = getRandomInteger(this.diameterUp, this.diameterDown - 32);
        //     x = getRandomInteger(this.diameterUp, this.diameterDown - 32);
        //     this.daqloons.push(
        //         new Daqloon(i, x, y, {
        //             diameterUp: this.diameterUp,
        //             diameterRight: this.diameterRight,
        //             diameterDown: this.diameterDown,
        //             diameterLeft: this.diameterLeft,
        //         })
        //     );
        // }
        return this.daqloons;
    }
    findHuntingDaqloon() {
        let distanceX = 0;
        let distanceY = 0;
        let nearbyDaqloon;
        for (const daqloon of this.daqloons) {
            if (!nearbyDaqloon) {
                nearbyDaqloon = daqloon;
                continue;
            }
            else {
                let nearbyX = Math.abs(nearbyDaqloon.drawX - daqloon.drawX);
                let nearbyY = Math.abs(nearbyDaqloon.drawY - daqloon.drawY);
                if (nearbyX < distanceX && nearbyY < distanceY) {
                    nearbyDaqloon = daqloon;
                }
            }
        }
        console.log(nearbyDaqloon.id);
        GamePieces.player.attackedBy = nearbyDaqloon.id;
        GamePieces.player.setHuntedStatus(true);
        // findOtherDaqloons() {
        //     const check = (object) => {
        //         return Math.abs(this.drawX - object.drawX) < 5 && Math.abs(this.drawY - object.drawY) < 5;
        //     };
        //     let nearby = GamePieces.daqloon.findIndex(check, this);
        //     let nearbyX = 1;
        //     let nearbyY = 1;
        //     if (nearby != -1) {
        //         let nearbyDaqloon = GamePieces.daqloon[nearby];
        //         if (Math.abs(nearbyDaqloon.drawX - this.drawX) < 5) {
        //             nearbyX = 0;
        //         }
        //         if (Math.abs(nearbyDaqloon.drawY - this.drawY) < 5) {
        //             nearbyY = 0;
        //         }
        //     }
        //     return [nearbyX, nearbyY];
        // }
    }
}
