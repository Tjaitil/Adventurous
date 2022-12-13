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
    displayName: string;

    constructor(initCharacterData: ICharacter) {
        super(initCharacterData);

        this.displayName = initCharacterData.displayName;
    }
}
