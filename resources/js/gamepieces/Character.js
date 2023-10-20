import { BaseStaticGameObject } from "./BaseStaticGameObject.js";
export class Character extends BaseStaticGameObject {
    width = 38;
    height = 38;
    conversation;
    displayName;
    constructor(initCharacterData) {
        super(initCharacterData);
        this.displayName = initCharacterData.displayName;
    }
}
