import { BaseStaticGameObject } from "./BaseStaticGameObject.js";
import viewport from "../clientScripts/viewport.js";
import { StaticGameObject } from "../types/gamepieces/StaticGameObject.js";

export class Building extends BaseStaticGameObject {
    constructor(initBuildingData: StaticGameObject) {
        super(initBuildingData);

        this.width = initBuildingData.width * viewport.scale;
        this.height = initBuildingData.height * viewport.scale;
    }
}
