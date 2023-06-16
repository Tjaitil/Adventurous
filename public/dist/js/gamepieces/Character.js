import { BaseStaticGameObject } from "./BaseStaticGameObject.js";
export class Character extends BaseStaticGameObject {
    width = 42;
    height = 42;
    conversation;
    constructor(initCharacterData) {
        super(initCharacterData);
        this.y += 6;
        this.diameterUp += 6;
        this.displayName = initCharacterData.displayName.replace('_', ' ');
    }
}
