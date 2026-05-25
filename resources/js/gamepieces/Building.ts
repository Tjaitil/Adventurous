import { BaseStaticGameObject } from './BaseStaticGameObject';
import viewport from '../clientScripts/viewport';
import type { IBuildingGameObject } from '../types/gamepieces/GameObject';
import type { StaticGameObject } from '../types/gamepieces/StaticGameObject';

export class Building
  extends BaseStaticGameObject
  implements IBuildingGameObject
{
  public displayName: string;

  constructor(initBuildingData: StaticGameObject) {
    super(initBuildingData);
    this.type = 'building';
    this.width = initBuildingData.width * viewport.scale;
    this.height = initBuildingData.height * viewport.scale;
    this.displayName = initBuildingData.displayName;
  }
}
