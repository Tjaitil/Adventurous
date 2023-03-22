import { BaseStaticGameObject } from "./BaseStaticGameObject.js";
import { StaticGameObject } from "../types/gamepieces/StaticGameObject.js";

export interface ICharacter extends StaticGameObject {
    conversation: boolean;
    displayName: string;
}

export class Character extends BaseStaticGameObject implements ICharacter {
    width: number = 38;
    height: number = 38;
    conversation: boolean;

    constructor(initCharacterData: ICharacter) {
        super(initCharacterData);
        this.y += 6;
        this.diameterUp += 6;
        this.displayName = initCharacterData.displayName.replace('_', ' ');
    }
}
