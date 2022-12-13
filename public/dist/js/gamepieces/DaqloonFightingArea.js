import { Daqloon } from "./../gamepieces/Daqloon.js";
import { getRandomInteger } from "../utilities/getRandomInteger.js";
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
        console.log(this);
        this.diameterUp = this.y;
        this.diameterLeft = this.x;
        this.diameterDown = this.y + this.height;
        this.diameterRight = this.x + this.width;
    }
    loadDaqloons() {
        let daqloons = [];
        let y;
        let x;
        for (let i = 0; i < this.daqloon_amount; i++) {
            y = getRandomInteger(this.diameterUp, this.diameterDown - 32);
            x = getRandomInteger(this.diameterUp, this.diameterDown - 32);
            this.daqloons.push(new Daqloon(i, x, y, {
                diameterUp: this.diameterUp,
                diameterRight: this.diameterRight,
                diameterDown: this.diameterDown,
                diameterLeft: this.diameterLeft,
            }));
        }
        return this.daqloons;
    }
}
