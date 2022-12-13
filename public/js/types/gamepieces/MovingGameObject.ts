import { GameObject } from "./GameObject";
import { Daqloon } from "../../gamepieces/Daqloon.js";
import { Player } from "../../gamepieces/Player.js";

export type DirectionBlockedCheck = "blocked" | "";

export interface MovingGameObject extends GameObject {
    type: string;
    sprite: HTMLImageElement;
    src: string;
    drawX: number;
    drawY: number;
    visible: boolean;
    up: DirectionBlockedCheck;
    right: DirectionBlockedCheck;
    down: DirectionBlockedCheck;
    left: DirectionBlockedCheck;
    movementSpeed: number;
}

export type MovingGameObjectTypes = Player | Daqloon;

// export enum MovingGameObjectTypes {
//     player = player
//     daqloon = "daqloon",
// }
