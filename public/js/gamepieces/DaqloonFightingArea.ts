import { Daqloon } from "./../gamepieces/Daqloon.js";
import { GameObject } from "../types/gamepieces/GameObject.js";
import { getRandomInteger } from "../utilities/getRandomInteger.js";

export interface IDaqloonFightingArea extends GameObject {
    daqloon_amount: number;
}

export class DaqloonFightingArea implements IDaqloonFightingArea {
    daqloon_amount: number;
    daqloons: Daqloon[] = [];
    diameterUp: number;
    diameterLeft: number;
    diameterDown: number;
    diameterRight: number;
    width: number;
    height: number;
    x: number;
    y: number;
    noCollision = true;
    type: "object";

    constructor(initData: IDaqloonFightingArea) {
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

    public loadDaqloons(): Daqloon[] {
        let daqloons = [];
        let y;
        let x;
        for (let i = 0; i < this.daqloon_amount; i++) {
            y = getRandomInteger(this.diameterUp, this.diameterDown - 32);
            x = getRandomInteger(this.diameterUp, this.diameterDown - 32);

            this.daqloons.push(
                new Daqloon(i, x, y, {
                    diameterUp: this.diameterUp,
                    diameterRight: this.diameterRight,
                    diameterDown: this.diameterDown,
                    diameterLeft: this.diameterLeft,
                })
            );
        }

        return this.daqloons;
    }
}
