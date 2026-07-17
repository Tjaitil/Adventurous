import type { StoreItemResponse } from './Responses/StoreItemResponse';
import type { ArmoryWarrior } from './Warrior';
import type { MineCountdownResponse } from './Mine';
import type { CropCountdownResponse } from './crops';
import type { CropResource } from './CropResource';
import type { MineralResource } from './MineralResource';

type UnwrapDataLoader<T> = T[keyof T] extends () => Promise<infer R>
  ? R
  : never;

type MappedDataLoader<T> = {
  [K in keyof T]: UnwrapDataLoader<Pick<T, K>>;
};

export type SmithyDataLoader = {
  store_items: () => Promise<StoreItemResponse>;
};
export type SmithyDataLoaderResponse =
  UnwrapDataLoader<SmithyDataLoader>['data'];

export type TravelbureauDataLoader = {
  store_items: () => Promise<StoreItemResponse>;
};
export type TravelbureauDataLoaderResponse =
  UnwrapDataLoader<TravelbureauDataLoader>['data'];

export type BakeryDataLoader = {
  store_items: () => Promise<StoreItemResponse>;
};
export type BakeryDataLoaderResponse =
  UnwrapDataLoader<BakeryDataLoader>['data'];

export type ArmoryDataLoader = {
  warriors: () => Promise<ArmoryWarrior[]>;
};
export type ArmoryDataLoaderResponse = MappedDataLoader<ArmoryDataLoader>;

export type ArcheryShopDataLoader = {
  store_items: () => Promise<StoreItemResponse>;
};
export type ArcheryShopDataLoaderResponse =
  UnwrapDataLoader<ArcheryShopDataLoader>['data'];

export type ZinsStoreDataLoader = {
  store_items: () => Promise<StoreItemResponse>;
};
export type ZinsStoreDataLoaderResponse =
  UnwrapDataLoader<ZinsStoreDataLoader>['data'];

export interface GetSkillActionDataRequest {
  workforce: {
    avail_workforce: number;
    efficiency_level: number;
  };
  crops: CropResource[];
  minerals: MineralResource[];
  permits?: number;
}

export type MineDataLoader = {
  action_items: () => Promise<GetSkillActionDataRequest>;
  countdown: () => Promise<MineCountdownResponse>;
};

export type MineDataLoaderResponse = MappedDataLoader<MineDataLoader>;

export type CropsDataLoader = {
  action_items: () => Promise<GetSkillActionDataRequest>;
  countdown: () => Promise<CropCountdownResponse>;
};

export type CropsDataLoaderResponse = MappedDataLoader<CropsDataLoader>;
