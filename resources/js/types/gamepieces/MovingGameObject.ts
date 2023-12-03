import { GameObject } from "./GameObject";
import { Daqloon } from "../../gamepieces/Daqloon";
import { Player } from "../../gamepieces/Player";

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
    currentAnimation: string;
}

export type MovingGameObjectTypes = Player | Daqloon;

// export enum MovingGameObjectTypes {
//     player = player
//     daqloon = "daqloon",
// }
