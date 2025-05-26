import { BaseStaticGameObject } from './BaseStaticGameObject';
import viewport from '../clientScripts/viewport';
import { StaticGameObject } from '../types/gamepieces/StaticGameObject';

export class Building extends BaseStaticGameObject {
  constructor(initBuildingData: StaticGameObject) {
    super(initBuildingData);
    this.type = 'building';
    this.width = initBuildingData.width * viewport.scale;
    this.height = initBuildingData.height * viewport.scale;
  }
}
