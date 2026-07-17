import type archeryShopModule from '@/buildingScripts/archeryshop';
import type bakeryModule from '@/buildingScripts/bakery';
import type merchantModule from '@/buildingScripts/merchant';
import type smithyModule from '@/buildingScripts/smithy';
import type stockpileModule from '@/buildingScripts/stockpile';
import type travelBureauModule from '@/buildingScripts/travelbureau';
import type workforceLodgeModule from '@/buildingScripts/workforcelodge';
import type zinsStoreModule from '@/buildingScripts/zinsstore';

type BuildingModuleMapping = {
  bakery: typeof bakeryModule;
  travelbureau: typeof travelBureauModule;
  stockpile: typeof stockpileModule;
  mine: null;
  crops: null;
  zinsstore: typeof zinsStoreModule;
  merchant: typeof merchantModule;
  workforcelodge: typeof workforceLodgeModule;
  smithy: typeof smithyModule;
  archeryshop: typeof archeryShopModule;
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

export type VuePage = 'armory' | 'crops' | 'mine';

const vuePages: VuePage[] = ['armory', 'crops', 'mine'];

export function isVuePage(page: BuildingName): page is VuePage {
  return vuePages.includes(page as VuePage);
}
