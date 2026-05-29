import type CropsModule from '@/buildingScripts/crops';
import type merchantModule from '@/buildingScripts/merchant';
import type MineModule from '@/buildingScripts/mine';
import type stockpileModule from '@/buildingScripts/stockpile';
import type workforceLodgeModule from '@/buildingScripts/workforcelodge';

type BuildingModuleMapping = {
  bakery: null;
  travelbureau: null;
  stockpile: typeof stockpileModule;
  mine: MineModule;
  crops: CropsModule;
  zinsstore: null;
  merchant: typeof merchantModule;
  workforcelodge: typeof workforceLodgeModule;
  smithy: null;
  archeryshop: null;
  armory: null;
};

export enum Buildings {
  BAKERY = 'bakery',
  TRAVELBUREAU = 'travelbureau',
  STOCKPILE = 'stockpile',
  MINE = 'mine',
  CROPS = 'crops',
  ZINSSTORE = 'zinsstore',
  MERCHANT = 'merchant',
  WORKFORCELODGE = 'workforcelodge',
  SMITHY = 'smithy',
  ARCHERYSHOP = 'archeryshop',
  ARMORY = 'armory',
}

export type BuildingName = keyof BuildingModuleMapping;

export type VuePage = 'armory' | 'smithy' | 'bakery' | 'archeryshop' | 'travelbureau' | 'zinsstore';

export function isVuePage(page: BuildingName): page is VuePage {
  return ['armory', 'smithy', 'bakery', 'archeryshop', 'travelbureau', 'zinsstore'].includes(page);
}
