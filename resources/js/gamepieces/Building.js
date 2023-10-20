import { BaseStaticGameObject } from "./BaseStaticGameObject.js";
import viewport from "../clientScripts/viewport.js";
export class Building extends BaseStaticGameObject {
    constructor(initBuildingData) {
        super(initBuildingData);
        this.width = initBuildingData.width * viewport.scale;
        this.height = initBuildingData.height * viewport.scale;
    }
}
